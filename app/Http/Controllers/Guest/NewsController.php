<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsCommentLike;
use App\Traits\EditorUploadImage;
use App\Traits\CommentTrait;
use Storage;
use DB;
use Log;

class NewsController extends Controller
{
    use EditorUploadImage;
    use CommentTrait;

    public function index()
    {
        $news = News::orderBy('id', 'desc')->get();
        return view('asbab.news', compact('news'));
    }

    public function data()
    {
        $news = News::orderBy('id', 'desc')->get();
        $baseUrl = route('asbab.home');
        return response()->json(['news' => $news, 'baseUrl' => $baseUrl], 200);
    }

    public function details($slug)
    {
        $blog = News::where('slug', $slug)->first();
        visits($blog)->increment();
        $visits = visits($blog)->count();
        $blogId = $blog->id;
        $comments = NewsComment::where('news_id', $blogId)->where('parent_id', 0)->orderBy('updated_at', 'desc')->paginate(4);        
        $subcomments = NewsComment::where('news_id', $blogId)->orderBy('updated_at', 'asc')->get();  
        return view('asbab.article', compact('blog','comments','subcomments','visits'));
    }

    public function getComment(Request $request, $id)
    {
        $comments = NewsComment::where('news_id', $id)->where('parent_id', '0')->get();
        $subcomments = NewsComment::where('news_id', $id)->get();
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
        $comment = NewsComment::find($id);
        $subcomments = NewsComment::where('news_id', $comment->news_id)->orderBy('updated_at', 'asc')->get();
        $data = $this->getHtmlsComment($comment, $subcomments, $id, 'news');
        return response()->json([
            'commhtmls' => $data['htmls'],
            'count' => $data['count'],
            'limit' => $request->limit + 4,
            'baseUrl' => route('asbab.home')
        ], 200);
    }

    public function add_comment(Request $request, $id)
    {
        $validator = $request->validate([
            'details' => 'required'
        ]);
        
        try {
            DB::beginTransaction();
            $comment = NewsComment::create([
                'news_id' => $id,
                'user_id' => auth()->id(),
                'parent_id' => $request->parent_id,
                'comment' => ''
            ]);

            $details = $this->SaveUploadEditorImage($request, 'news_comment/'.$comment->id);
            $comment->update([
                'comment' => $details
            ]);
            DB::commit();
            if ($comment->user_id == 0) {
                $user->avatar = 'images/avatar/1631765133.png';
                $user->name = 'Asbab Furniture Shop';
            } else {
                $user = $comment->users;
            }
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
                                            <a class="btn-remove-comment" data-url="'.route('asbab.news.remove_comment', ['id' => $comment->id]).'" href="#"><i class="fa fa-trash-alt"></i></a>
                                            <span class="btn-show-subcomment" data-url="'.route('asbab.news.getSubComm', ['id' => $comment->id]).'"><i class="far fa-comments"></i></span><span class="ml-1 show-count-subcomment">0</span></a>
                                            <a href="#"><span class="btn-like-comment" data-url="'.route('asbab.news.like_comment', ['id' => $comment->id]).'"><i class="far fa-thumbs-up"></i></span><span class="ml-1 show-like-comment">0</span></a>
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
                                        <a class="btn-remove-comment" data-url="'.route('asbab.news.remove_comment', ['id' => $comment->id]).'" href="#"><i class="fa fa-trash-alt"></i></a>
                                        <a href="#"><span class="btn-like-comment" data-url="'.route('asbab.news.like_comment', ['id' => $comment->id]).'"><i class="far fa-thumbs-up"></i></span><span class="ml-1 show-like-comment">0</span></a>
                                    </div>
                                </div>';
            }
            return response()->json([
                'htmlComment' => $htmlComment,
                'parent_id' => $request->parent_id
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
        $uploadDir = 'images/upload/news_comment/'.$id;
        Storage::disk('public')->deleteDirectory($uploadDir);
        $comment = NewsComment::find($id);
        $parent_id = $comment->parent_id;
        if($parent_id == 0) {
            $comments = NewsComment::all();
            foreach ($comments as $comm) {
                if (id_parent($comm->parent_id, $comments, 'parent_id', 'id') == $id) {
                    $uploadSubDir = 'public/upload/news_comment/'.$comm->id;
                    Storage::disk('public')->deleteDirectory($uploadSubDir);
                    $comm->delete();
                }
            }
        }
        $comment->delete();

        $count = NewsComment::all()->count();
        return response()->json([
            'parent_id' => $parent_id,
            'count' => $count
        ], 200);
    }

    public function like_comment(Request $request, $id)
    {
        if($request->type == 0) {
            $blog = News::find($id);
            $like_old = $blog->likes;
            $arrOld = trim($like_old) === '' ? [] : explode(',', $like_old);
            if(count($arrOld) === 0) {
                $like_new = auth()->id();
                $count_like = 1;
            } else {
                $arrNew = array_diff($arrOld, ['']);
                if(in_array(auth()->id(), $arrNew)) {
                    $arrNew2 = array_diff($arrNew, [auth()->id()]);
                    $like_new = implode(',', $arrNew2);
                    $count_like = count($arrNew2);
                } else {
                    $like_new = implode(',', $arrNew).','.auth()->id();
                    $count_like = count($arrNew) + 1;
                }
            }
            $blog->update([
                'likes' => $like_new
            ]);
        } else {
            $like = NewsCommentLike::where('news_comment_id', $id)->where('user_id', auth()->id())->first();
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
                NewsCommentLike::create([
                    'news_comment_id' => $id,
                    'user_id' => auth()->id(),
                    'like' => 1
                ]);
            }
    
            $count_like = NewsCommentLike::where('news_comment_id', $id)->where('like', '1')->get()->count();
        }        
        return response()->json([
            'message' => 'success',
            'likes' =>  $count_like
        ], 200);
    }
}
