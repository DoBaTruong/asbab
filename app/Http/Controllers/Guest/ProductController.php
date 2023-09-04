<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\User;
use App\Models\Category;
use App\Models\Rating;
use App\Models\ProductComment;
use App\Models\ProductCommentLike;
use App\Traits\EditorUploadImage;
use App\Traits\CommentTrait;
use Storage;
use DB;
use Log;

class ProductController extends Controller
{
    use EditorUploadImage;
    use CommentTrait;
    
    public function show($slug)
    {
        $brands = Brand::all();
        $product = Product::where('slug', $slug)->first();
        $products = Product::all();
        $productId = $product->id;
        $ratcheck = Rating::where('product_id', $productId)->where('user_id', auth()->id())->first();
        $prarentID = id_parent ($product->category_id, Category::all(), 'parent_id', 'id');
        $relateds = [];
        foreach ($products as $p) {
            if (id_parent ($p->category_id, Category::all(), 'parent_id', 'id') == $prarentID && $p->id !== $productId) {
                $relateds[] = $p;
            }
        }
        return view('asbab.product', compact('product','brands','ratcheck', 'relateds'));
    }

    public function getComment(Request $request, $id)
    {
        $comments = ProductComment::where('product_id', $id)->where('parent_id', '0')->get();

        $subcomments = ProductComment::where('product_id', $id)->get();
        foreach($comments as $comm) {
            $comm->date_print = date('D-H:i M/Y', strtotime($comm->updated_at));
            $count = 0;
            foreach ($subcomments->where('parent_id', '>', '0') as $sub) {
                $parent = id_parent($sub->parent_id, $subcomments, 'parent_id', 'id');
                if($parent == $comm->id) {
                    $count++;
                }
            }
            $comm->count_comm = $count;
            $like_arr_user = $comm->likes->pluck('user_id')->toArray();
            $auth_id = auth()->id();
            $comm->auth_like = in_array($auth_id, $like_arr_user);
        }
        $paginationhtmls = $this->paginate($comments, $request->page);
        return response()->json([
            'comments' => $comments,
            'baseUrl' => route('asbab.home'),
            'pagination' => $paginationhtmls
        ], 200);
    }

    public function getSubComm(Request $request, $id)
    {
        $comment = $comment = ProductComment::find($id);
        $subcomments = ProductComment::where('product_id', $comment->product_id)->orderBy('updated_at', 'asc')->get();
        $data = $this->getHtmlsComment($comment, $subcomments, $id);
        return response()->json([
            'commhtmls' => $data['htmls'],
            'count' => $data['count'],
            'limit' => $request->limit + 4,
            'baseUrl' => route('asbab.home')
        ], 200);
    }

    public function stars(Request $request, $id)
    {
        $validator = $request->validate([
            'rating' => 'required'
        ]);

        try {
            DB::beginTransaction();
            Rating::create([
                'product_id' => $id,
                'user_id' => auth()->id(),
                'rate' => $request->rating
            ]);
            $rating = Rating::where('product_id', $id)->get()->avg('rate');
            DB::commit();
            return response()->json($rating);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }
 
    public function add_comment(Request $request, $id)
    {
        $validator = $request->validate([
            'details' => 'required'
        ]);
        
        try {
            DB::beginTransaction();
            $comment = ProductComment::create([
                'product_id' => $id,
                'user_id' => auth()->id(),
                'parent_id' => $request->parent_id,
                'comment' => ''
            ]);

            $details = $this->SaveUploadEditorImage($request, 'product_comment/'.$comment->id);
            $comment->update([
                'comment' => $details
            ]);
            DB::commit();
            $user = $comment->users;
            if ($request->parent_id == 0) {
                $htmlComment = '<div class="comment-group">
                                    <div data-id="'.$comment->id.'" class="comment-item">
                                        <div class="comment-avatar">
                                            <img src="'.asset($user->avatar).'" alt="User Avatar" />
                                        </div>
                                        <div class="comment-detail">
                                            <span class="comm-name">'.$user->name.'</span>
                                            <span class="comm-time">'.date('D-H:i M/Y', strtotime($comment->updated_at)).'</span>
                                            <p class="comm-text">'.$comment->comment.'</p>
                                        </div>
                                        <div class="comm-action">
                                            <a class="btn-reply-comment" href="#"><i class="fa fa-pen-fancy"></i></a>
                                            <a class="btn-remove-comment" data-url="'.route('asbab.product.remove_comment', ['id' => $comment->id]).'" href="#"><i class="fa fa-trash-alt"></i></a>
                                            <a href="#" class="text-dark"><span class="btn-show-subcomment" data-url="'.route('asbab.product.getSubComm', ['id' => $comment->id]).'"><i class="far fa-comments"></i></span><span class="ml-1 show-count-subcomment">0</span></a>
                                            <a href="#" class="text-dark"><span class="btn-like-comment" data-url="'.route('asbab.product.like_comment', ['id' => $comment->id]).'"><i class="far fa-thumbs-up"></i></span><span class="ml-1 show-like-comment">0</span></a>
                                        </div>
                                    </div>
                                </div>';
            } else {
                $htmlComment = '<div data-id="'.$comment->id.'" class="comment-item comm-rep">
                                    <div class="comment-avatar">
                                        <img src="'.asset($user->avatar).'" alt="User Avatar" />
                                    </div>
                                    <div class="comment-detail">
                                        <span class="comm-name">'.$user->name.'</span>
                                        <span class="comm-time">'.date('D-H:i M/Y', strtotime($comment->updated_at)).'</span>
                                        <p class="comm-text">'.$comment->comment.'</p>
                                    </div>
                                    <div class="comm-action">
                                        <a class="btn-reply-comment" href="#"><i class="fa fa-pen-fancy"></i></a>
                                        <a class="btn-remove-comment" data-url="'.route('asbab.product.remove_comment', ['id' => $comment->id]).'" href="#"><i class="fa fa-trash-alt"></i></a>
                                        <a href="#" class="text-dark"><span class="btn-like-comment" data-url="'.route('asbab.product.like_comment', ['id' => $comment->id]).'"><i class="far fa-thumbs-up"></i></span><span class="ml-1 show-like-comment">0</span></a>
                                    </div>
                                </div>';
            }
            $comments = ProductComment::where('product_id', $id)->where('parent_id', 0)->orderBy('updated_at', 'desc')->paginate(4);        
            $paginations = $this->paginate($comments);
            return response()->json([
                'htmlComment' => $htmlComment,
                'parent_id' => $request->parent_id,
                'baseUrl' => route('asbab.home'),
                'paginations' => $paginations
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function remove_comment($id)
    {
        $uploadDir = 'images/upload/product_comment/'.$id;
        Storage::disk('public')->deleteDirectory($uploadDir);
        $comment = ProductComment::find($id);
        $parent_id = $comment->parent_id;
        if($parent_id == 0) {
            $comments = ProductComment::all();
            foreach ($comments as $comm) {
                if (id_parent($comm->parent_id, $comments, 'parent_id', 'id') == $id) {
                    $uploadSubDir = 'public/upload/product_comment/'.$comm->id;
                    Storage::disk('public')->deleteDirectory($uploadSubDir);
                    $comm->delete();
                }
            }
        }
        $comment->delete();
        return response()->json([
            'parent_id' => $parent_id
        ], 200);
    }

    public function like_comment($id)
    {
        $like = ProductCommentLike::where('product_comment_id', $id)->where('user_id', auth()->id())->first();
        if(isset($like)) {
            if($like->like == 1) {
                $like->update([
                    'like' => 0
                ]);
            } else {
                $like->update([
                    'like' => 1
                ]);
            }
        } else {
            ProductCommentLike::create([
                'product_comment_id' => $id,
                'user_id' => auth()->id(),
                'like' => 1
            ]);
        }

        $count_like = ProductCommentLike::where('product_comment_id', $id)->where('like', '1')->get()->count();

        return response()->json([
            'message' => 'success',
            'likes' =>  $count_like
        ], 200);
    }

    public function wishlist(Request $request)
    {
        if (auth()->user()) {
            $arrPrdID = auth()->user()->wishlist !== null && trim(auth()->user()->wishlist) !== '' ? explode(',', auth()->user()->wishlist) : [];
            $products = Product::whereIn('id', $arrPrdID)->orderBy('price', 'asc')->get();
        } else {
            $arrPrdID = session()->get('wishlist') !== null ? explode(',',session()->get('wishlist')) : [];
            $products = Product::whereIn('id', $arrPrdID)->orderBy('price', 'asc')->get();
        }
        $pagi = $this->paginate($products, $request->page, $request->items);
        
        return response()->json([
            'products' => $products,
            'baseUrl' => route('asbab.home'),
            'pagination' => $pagi
        ], 200);
    }

    public function remove_wishlist($id)
    {
        if (auth()->user()) {
            $ID_older = auth()->user()->wishlist;
            if (!empty($ID_older)) {
                $ID_new = array_diff(explode(',', $ID_older), ['', $id]);
                $w = auth()->user()->update([
                    'wishlist' => implode(',', $ID_new)
                ]);
            } else {
                $w = auth()->user()->update([
                    'wishlist' => $id
                ]);
            }
        } else {
            $wishlist = session()->get('wishlist');
            if (!empty($wishlist)) {
                $wishlist .= ','.$id; 
            } else {
                $wishlist = $id;
            }
            session()->put('wishlist', $wishlist);
            $w = session()->get('wishlist');
        }
        
        return response()->json($w, 200);
        
    }

    public function add_wishlist(Request $request)
    {
        if (auth()->user()) {
            $arrPrdID = auth()->user()->wishlist !== null && trim(auth()->user()->wishlist) !== '' ? explode(',', auth()->user()->wishlist) : [];
            if (!empty($arrPrdID)) {
                if (!in_array($request->id, $arrPrdID)) {
                    array_push($arrPrdID, $request->id);
                }
                $strId = implode(',', $arrPrdID);
            } else {
                $strId = $request->id;
            }
            $updated = auth()->user()->update([
                'wishlist' => $strId
            ]);
        } else {
            $arrID = session()->get('wishlist') !== null ? explode(',', session()->get('wishlist')) : [];
            if (!empty($arrID)) {
                if (!in_array($request->id, $arrID)) {
                    array_push($arrID, $request->id);
                }
                $strId = implode(',', $arrID);
            } else {
                $strId = $request->id;
            }
            $updated = session()->put('wishlist', $strId);
        }
        return response()->json($updated);
        
    }

    public function compare(Request $request)
    {
        $arrPrdID = $request->data;
        $products = [];
        if (!empty($arrPrdID)) {
            foreach (Product::whereIn('id', $arrPrdID)->orderBy('price', 'asc')->get() as $p) {
                $products[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image' => $p->feature_image_path,
                    'status' => $p->quantity - $p->sell,
                    'price' => $p->price,
                    'stars' => $p->rates->avg('rate') !== null ? $p->rates->avg('rate') : 0
                ];
            }
        }

        return response()->json([
            'products' => $products,
            'baseUrl' => route('asbab.home')
        ], 200);
    }
}
