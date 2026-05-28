<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTimeline extends Model
{
    protected $table = 'order_timeline';
    protected $fillable = ['order_id', 'status', 'remark'];
    protected $dates = ['created_at'];

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
