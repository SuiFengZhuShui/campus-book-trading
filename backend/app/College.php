<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class College extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'sort'];

    public function majors()
    {
        return $this->hasMany(Major::class);
    }
}
