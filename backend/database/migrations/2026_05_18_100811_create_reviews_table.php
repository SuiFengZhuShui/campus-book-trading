<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->unique()->comment('订单（一个订单一条评价）');
            $table->unsignedBigInteger('user_id')->comment('评价人（买家）');
            $table->unsignedBigInteger('book_id')->comment('书籍');
            $table->tinyInteger('book_rating')->unsigned()->comment('书况评分 1-5');
            $table->tinyInteger('service_rating')->unsigned()->comment('服务评分 1-5');
            $table->string('comment', 500)->nullable()->comment('文字评价');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
