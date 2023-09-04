<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductComment;
use App\Models\News;
use App\Models\NewsComment;
use DataTables;
use Storage;
use Log;
use DB;

class CommentController extends Controller
{
    public function PRDcomments()
    {
        $products = [];
        $incre = 0;
        foreach (Product::all() as $p) {
            if ($p->comments->count() > 0) {
                $incre++;
                $p->incre = $incre;
                $products[] = $p;
            }
        }
         
        return DataTables::of($products)
            ->addColumn('stt', function ($product) {
                return $product->incre;
            })
            ->addColumn('product', function ($product) {
                return $product->name;
            })
            ->addColumn('comments', function ($product) {
                return $product->comments->count();
            })
            ->addColumn('rating', function ($product) {
                return ($product->rates !== null ? $product->rates->count() : 0);
            })
            ->addColumn('action', function ($product) {
                return '<a href="'.route('admin.comment.product.details', ['slug' => $product->slug]).'" class="btn btn-info">Details</a>';
            })
            ->rawColumns(['stt', 'product', 'comments', 'rating', 'action'])
            ->make(true);
    }

    public function PRDcommDT($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $comments = ProductComment::where('product_id', $product->id)->orderBy('created_at', 'desc')->paginate(9);
        foreach ($comments->where('parent_id', '>', 0) as $comm) {
            foreach ($comments as $comment) {
                if ($comment->id === $comm->parent_id) {
                    $c = $comment;
                }
            }
            $comm->reply_for_user_name = $c !== null ? ($c->user_id != 0 ? $c->users->name : 'Asbab Furniture Shop') : '';
        }
        return view('admin.comment.show', compact('comments','product'));
    }

    public function PRDreply(Request $request, $slug)
    {
        $comment = ProductComment::create([
            'product_id' => Product::where('slug', $slug)->first()->id,
            'user_id' => 0,
            'comment' => $request->reply,
            'parent_id' => $request->parent_id
        ]);
        
        return response()->json([
            'message' => 'success',
            'code' => 200
        ]);
    }

    public function PRDdelete(Request $request,$slug) 
    {
        $id = $request->id;
        settype($id, 'integer');
        $dataID = [$id];
        $comment = ProductComment::find($request->id);
        $comments = ProductComment::all();
        $comment->likes()->delete();
        $uploadDir = 'images/upload/product_comment/'.$id;
        
        Storage::disk('public')->deleteDirectory($uploadDir);

        foreach ($comments->where('parent_id', '>', 0) as $comm) 
        {
           if (id_parent($comm->parent_id, $comments, 'parent_id', 'id', $comment->parent_id) == $request->id)  
           {
                $uploadDirSub = 'images/upload/product_comment/'.$comm->id;
                Storage::disk('public')->deleteDirectory($uploadDirSub);
                array_push($dataID, $comm->id);
                $comm->likes()->delete();
           }
        }

        ProductComment::destroy($dataID);
        return response()->json([
            'message' => 'success',
            'code' => 200
        ]);
    }

    public function NEWScomments()
    {
        $posts = [];
        $incre = 0;
        foreach (News::all() as $n) {
            if ($n->comments->count() > 0) {
                $incre++;
                $n->incre = $incre;
                $posts[] = $n;
            }
        }
         
        return DataTables::of($posts)
            ->addColumn('stt', function ($post) {
                return $post->incre;
            })
            ->addColumn('post', function ($post) {
                return $post->title;
            })
            ->addColumn('comments', function ($post) {
                return $post->comments->count();
            })
            ->addColumn('view', function ($post) {
                return visits($post)->count();
            })
            ->addColumn('like', function ($post) {
                return ($post->likes !== null ? explode(',', $post->likes)->count() : 0);
            })
            ->addColumn('action', function ($post) {
                return '<a href="'.route('admin.comment.news.details', ['slug' => $post->slug]).'" class="btn btn-info">Details</a>';
            })
            ->rawColumns(['stt', 'post', 'comments', 'view', 'like', 'action'])
            ->make(true);
    }

    public function NEWScommDT($slug)
    {
        $post = News::where('slug', $slug)->first();
        $comments = NewsComment::where('news_id', $post->id)->orderBy('created_at', 'desc')->paginate(9);
        foreach ($comments->where('parent_id', '>', 0) as $comm) {
            foreach ($comments as $comment) {
                if ($comment->id === $comm->parent_id) {
                    $c = $comment;
                }
            }
            $comm->reply_for_user_name = $c !== null ? ($c->user_id != 0 ? $c->users->name : 'Asbab Furniture Shop') : '';
        }
        return view('admin.comment.post', compact('comments','post'));
    }

    public function NEWSreply(Request $request, $slug)
    {
        $comment = NewsComment::create([
            'news_id' => News::where('slug', $slug)->first()->id,
            'user_id' => 0,
            'comment' => $request->reply,
            'parent_id' => $request->parent_id
        ]);
        
        return response()->json([
            'message' => 'success',
            'code' => 200
        ]);
    }

    public function NEWSdelete(Request $request, $slug) 
    {
        $id = $request->id;
        settype($id, 'integer');
        $dataID = [$id];
        $comment = NewsComment::find($request->id);
        $comments = NewsComment::all();
        $comment->likes()->delete();
        $uploadDir = 'images/upload/news_comment/'.$id;
        
        Storage::disk('public')->deleteDirectory($uploadDir);

        foreach ($comments->where('parent_id', '>', 0) as $comm) 
        {
           if (id_parent($comm->parent_id, $comments, 'parent_id', 'id', $comment->parent_id) == $request->id)  
           {
                $uploadDirSub = 'images/upload/news_comment/'.$comm->id;
                Storage::disk('public')->deleteDirectory($uploadDirSub);
                array_push($dataID, $comm->id);
                $comm->likes()->delete();
           }
        }

        NewsComment::destroy($dataID);
        return response()->json([
            'message' => 'success',
            'code' => 200
        ]);
    }
}
