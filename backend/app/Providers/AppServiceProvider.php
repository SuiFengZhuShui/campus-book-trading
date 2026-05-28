<?php

namespace App\Providers;

use App\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        View::composer('web.layouts.app', function ($view) {
            $view->with('categories', Category::orderBy('sort')->get());
        });
    }
}
