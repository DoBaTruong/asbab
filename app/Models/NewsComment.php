<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NewsCommentLike;
use App\Models\User;

class NewsComment extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['users', 'likes'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(NewsCommentLike::class, 'news_comment_id');
    }
}
