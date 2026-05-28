<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Want extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'author', 'publisher',
        'max_price', 'acceptable_condition', 'status', 'expires_at',
    ];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function fulfillments()
    {
        return $this->hasMany(WantFulfillment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
