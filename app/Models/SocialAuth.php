<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class SocialAuth extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['users'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
