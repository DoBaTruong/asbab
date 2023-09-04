<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductComment;
use App\Models\Rating;
use App\Models\Tag;
use App\Models\ProductImage;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['rates'];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id')->withTimestamps();
    }

    public function rates ()
    {
        return $this->hasMany(Rating::class, 'product_id');
    }

    public function comments ()
    {
        return $this->hasMany(ProductComment::class, 'product_id');
    }
}
