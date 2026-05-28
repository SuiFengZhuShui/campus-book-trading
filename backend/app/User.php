<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    protected $fillable = [
        'username', 'student_id', 'name', 'phone', 'password', 'role', 'avatar', 'status', 'api_token',
    ];

    protected $hidden = [
        'password', 'api_token', 'remember_token',
    ];

    public function sellingBooks()
    {
        return $this->hasMany(Book::class, 'seller_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wants()
    {
        return $this->hasMany(Want::class);
    }

    public function fulfillments()
    {
        return $this->hasMany(WantFulfillment::class, 'fulfiller_id');
    }
}
