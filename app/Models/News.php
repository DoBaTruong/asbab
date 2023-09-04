<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NewsComment;

class News extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function comments ()
    {
        return $this->hasMany(NewsComment::class, 'news_id');
    }
}
