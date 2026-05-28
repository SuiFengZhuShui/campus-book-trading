<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort')->get();

        return $this->success($categories->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->name];
        }));
    }
}
