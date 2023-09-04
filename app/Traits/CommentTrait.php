<?php
namespace App\Traits;

trait CommentTrait {
    public function getHtmlsComment($comment, $subcomments, $id, $view = 'product')
    {
        $commhtmls = '';
        $count = 0;
        foreach ($subcomments as $sub) {
            $parent = id_parent($sub->parent_id, $subcomments, 'parent_id', 'id');
            if($parent == $id) {
                $count++;
                if ($sub->user_id == 0) {
                    $user->avatar = 'images/avatar/1631765133.png';
                    $user->name = 'Asbab Furniture Shop';
                } else {
                    $user = $sub->users;
                }
                $likespanclass = '';
                $likeIclass = 'far';
                if(isset($sub->likes[0]) && $sub->likes[0]->user_id == auth()->id()) {
                    $likespanclass = 'text-danger';
                    $likeIclass = 'fa';
                }

                $commhtmls .= '<div data-id="'.$sub->id.'" class="comment-item comm-rep">
                                    <div class="comment-avatar">
                                        <img src="'.asset($user->avatar).'" alt="User Avatar" />
                                    </div>
                                    <div class="comment-detail">
                                        <span class="comm-name">'.$user->name.'</span>
                                        <span class="comm-time">'.date('D-H:i M/Y', strtotime($sub->updated_at)).'</span>
                                        <p class="comm-text">'.$sub->comment.'</p>
                                    </div>
                                    <div class="comm-action">
                                        <a class="btn-reply-comment" href="#"><i class="fa fa-pen-fancy"></i></a>
                                        <a class="btn-remove-comment" data-url="'.route('asbab.'.$view.'.remove_comment', ['id' => $sub->id]).'" href="#"><i class="fa fa-trash-alt"></i></a>
                                        <a href="#"><span class="btn-like-comment '.$likespanclass.'" data-url="'.route('asbab.'.$view.'.like_comment', ['id' => $sub->id]).'"><i class="'.$likeIclass.' fa-thumbs-up"></i></span><span class="ml-1 show-like-comment">'.$sub->likes->count().'</span></a>
                                    </div>
                                </div>';
            }
        }

        return [
            'htmls' => $commhtmls,
            'count' => $count
        ];
    }

    public function paginate($comments, $page = 1, $row = 4)
    {
        $page_count = ceil($comments->count() / $row);
        $paginations = '';
        if ($page_count > 1) {
            if($page > 1) {
                $prev = $page - 1;
                $paginations .= '<li><a href="#" data-page="'.$prev.'" class="page-link page__prev">Prev</a></li>';
            }
            for ($i = 1; $i <= $page_count; $i++) { 
                $active = $page == $i ? 'active' : '';
                $paginations .= '<li class="'.$active.'"><a href="#" data-page="'.$i.'" class="page-link page__item">'.$i.'</a></li>';
            }
            if ($page < $page_count) {
                $next = $page + 1;
                $paginations .= '<li><a href="#" data-page="'.$next.'" class="page-link page__next">Next</a></li>';
            }
        } 
        return $paginations;
    }
}