<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'author', 'publisher', 'isbn', 'category_id', 'course_id',
        'condition', 'original_price', 'price', 'cost_price', 'seller_id',
        'status', 'submitted_at', 'approved_at', 'received_at',
        'seller_paid', 'description', 'reject_reason',
    ];

    protected $dates = [
        'submitted_at', 'approved_at', 'received_at',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function images()
    {
        return $this->hasMany(BookImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOnSale($query)
    {
        return $query->whereIn('status', ['active']);
    }
}
