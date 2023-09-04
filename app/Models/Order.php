<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bill;
use App\Models\Coupon;
use App\Models\User;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function bills()
    {
        return $this->hasMany(Bill::class, 'order_id');
    }

    public function coupons()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shippers()
    {
        return $this->belongsTo(User::class, 'ship_id');
    }
}
