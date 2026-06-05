<?php

// 前台
Route::get('/', 'Web\HomeController@index');
Route::get('/search', 'Web\HomeController@search');
Route::get('/books/{id}', 'Web\HomeController@detail');
Route::get('/sell', 'Web\HomeController@sell');
Route::post('/sell', 'Web\HomeController@postSell');
Route::get('/login', 'Web\AuthController@loginForm');
Route::post('/login', 'Web\AuthController@login');
Route::post('/logout', 'Web\AuthController@logout');
Route::get('/register', 'Web\AuthController@registerForm');
Route::post('/register', 'Web\AuthController@register');

// 订单（需登录）
Route::get('/orders', 'Web\OrderController@index');
Route::get('/orders/{id}', 'Web\OrderController@detail');
Route::post('/orders/{id}/pay', 'Web\OrderController@pay');
Route::post('/orders/{id}/pickup', 'Web\OrderController@pickup');
Route::post('/orders/{id}/cancel', 'Web\OrderController@cancel');
Route::post('/orders/{id}/delete', 'Web\OrderController@delete');
Route::get('/buy/{bookId}', 'Web\OrderController@buyForm');
Route::post('/buy/{bookId}', 'Web\OrderController@buy');

// 求购
Route::get('/wants', 'Web\HomeController@wants');
Route::get('/wants/{id}', 'Web\HomeController@wantDetail');
Route::get('/post-want', 'Web\HomeController@postWant');
Route::post('/post-want', 'Web\HomeController@storeWant');

// 个人中心（需登录）
Route::get('/profile', 'Web\HomeController@profile');

// 购物车（需登录）
Route::get('/cart', 'Web\CartController@index');
Route::post('/cart/add', 'Web\CartController@add');
Route::post('/cart/remove/{id}', 'Web\CartController@remove');
Route::post('/cart/checkout', 'Web\CartController@checkout');

// 我的卖书（需登录）
Route::get('/my-sells', 'Web\HomeController@mySells');
Route::post('/my-sells/{id}/delete', 'Web\HomeController@deleteBook');

// Admin 后台
Route::prefix('admin')->namespace('Admin')->group(function () {
    Route::get('login', 'AuthController@loginForm')->name('admin.login');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', 'DashboardController@index')->name('admin.dashboard');
        Route::get('dashboard', 'DashboardController@index');

        // 审核管理
        Route::prefix('reviews')->group(function () {
            Route::get('/', 'ReviewController@index')->name('admin.reviews.index');
            Route::get('{id}', 'ReviewController@detail');
            Route::post('{id}/approve', 'ReviewController@approve');
            Route::post('{id}/reject', 'ReviewController@reject');
            Route::post('{id}/receive', 'ReviewController@receive');
        });

        // 书籍管理
        Route::prefix('books')->group(function () {
            Route::get('/', 'BookController@index')->name('admin.books.index');
            Route::get('{id}/edit', 'BookController@edit');
            Route::post('{id}/update', 'BookController@update');
            Route::post('{id}/remove', 'BookController@remove');
        });

        // 订单管理
        Route::prefix('orders')->group(function () {
            Route::get('/', 'OrderController@index')->name('admin.orders.index');
            Route::get('{id}', 'OrderController@detail');
            Route::post('{id}/confirm', 'OrderController@confirm');
            Route::post('{id}/pickup', 'OrderController@pickup');
            Route::post('{id}/cancel', 'OrderController@cancel');
            Route::post('{id}/delete', 'OrderController@destroy');
        });

        // 用户管理
        Route::get('users', 'UserController@index')->name('admin.users.index');
        Route::get('users/{id}/edit', 'UserController@edit');
        Route::post('users/{id}/update', 'UserController@update');
        Route::post('users/{id}/toggle-status', 'UserController@toggleStatus');
        Route::post('users/{id}/delete', 'UserController@destroy');

        // 求购管理
        Route::get('wants', 'WantController@index')->name('admin.wants.index');
        Route::get('wants/{id}/edit', 'WantController@edit');
        Route::post('wants/{id}/update', 'WantController@update');
        Route::post('wants/{id}/delete', 'WantController@destroy');

        // 分类管理
        Route::prefix('categories')->group(function () {
            Route::get('/', 'CategoryController@index')->name('admin.categories.index');
            Route::get('{id}/edit', 'CategoryController@edit');
            Route::post('/', 'CategoryController@store');
            Route::post('{id}', 'CategoryController@update');
            Route::post('{id}/delete', 'CategoryController@destroy');
            Route::post('sort', 'CategoryController@sort');
        });

        // 学院/专业/课程管理
        Route::prefix('colleges')->group(function () {
            Route::get('/', 'CollegeController@index')->name('admin.colleges.index');
            Route::post('/', 'CollegeController@store');
            Route::post('{id}', 'CollegeController@update');
            Route::post('{id}/delete', 'CollegeController@destroy');

            Route::post('{collegeId}/majors', 'CollegeController@storeMajor');
            Route::post('majors/{id}', 'CollegeController@updateMajor');
            Route::post('majors/{id}/delete', 'CollegeController@destroyMajor');

            Route::post('majors/{majorId}/courses', 'CollegeController@storeCourse');
            Route::post('courses/{id}', 'CollegeController@updateCourse');
            Route::post('courses/{id}/delete', 'CollegeController@destroyCourse');
        });
    });
});
