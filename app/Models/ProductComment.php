<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductCommentLike;
use App\Models\ProductComment;
use App\Models\Product;
use App\Models\User;

class ProductComment extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['users', 'likes', 'products'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(ProductCommentLike::class, 'product_comment_id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function comm_parents()
    {
        return $this->hasMany(ProductComment::class, 'parent_id');
    }
}
