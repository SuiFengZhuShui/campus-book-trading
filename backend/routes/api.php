<?php

Route::namespace('Api')->group(function () {

Route::prefix('auth')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');

    Route::middleware('api.auth')->group(function () {
        Route::post('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@me');
    });
});

// 公开（无需认证）
Route::get('books', 'BookController@index');
Route::get('books/{id}', 'BookController@show');
Route::get('categories', 'CategoryController@index');
Route::get('colleges', 'CollegeController@index');
Route::get('colleges/{id}/majors', 'CollegeController@majors');
Route::get('majors/{id}/courses', 'CollegeController@courses');
Route::get('wants', 'WantController@index');
Route::get('wants/{id}', 'WantController@show');

// 需认证
Route::middleware('api.auth')->group(function () {
    // 卖书
    Route::post('books/submit', 'BookController@submit');
    Route::get('my-books', 'BookController@myBooks');
    Route::delete('my-books/{id}', 'BookController@destroy');

    // 订单
    Route::post('orders', 'OrderController@store');
    Route::get('orders', 'OrderController@index');
    Route::get('orders/{id}', 'OrderController@show');
    Route::post('orders/{id}/pay', 'OrderController@pay');
    Route::post('orders/{id}/cancel', 'OrderController@cancel');
    Route::post('orders/{id}/pickup', 'OrderController@pickup');
    Route::post('orders/{id}/review', 'OrderController@review');

    // 求购
    Route::post('wants', 'WantController@store');
    Route::post('wants/{id}/fulfill', 'WantController@fulfill');

    // 上传
    Route::post('upload', 'UploadController@upload');
});

}); // end namespace Api
