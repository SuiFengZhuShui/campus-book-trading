<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WantFulfillment extends Model
{
    protected $fillable = ['want_id', 'fulfiller_id', 'book_id', 'status'];

    public function want()
    {
        return $this->belongsTo(Want::class);
    }

    public function fulfiller()
    {
        return $this->belongsTo(User::class, 'fulfiller_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
