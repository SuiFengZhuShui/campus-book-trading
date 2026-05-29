<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'order_no', 'buyer_id', 'total_amount', 'status', 'pickup_location',
        'paid_at', 'confirmed_at', 'picked_up_at', 'completed_at',
        'cancelled_at', 'cancel_reason',
    ];

    protected $dates = [
        'paid_at', 'confirmed_at', 'picked_up_at', 'completed_at', 'cancelled_at',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function timeline()
    {
        return $this->hasMany(OrderTimeline::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
