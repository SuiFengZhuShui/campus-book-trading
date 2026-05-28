<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = ['major_id', 'name', 'sort'];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
