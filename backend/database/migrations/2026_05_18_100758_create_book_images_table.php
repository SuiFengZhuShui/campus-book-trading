<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookImagesTable extends Migration
{
    public function up()
    {
        Schema::create('book_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('book_id')->comment('书籍');
            $table->string('path', 255)->comment('图片路径');
            $table->enum('type', ['cover', 'inner', 'spine', 'other'])->default('other')->comment('图片类型');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamp('created_at')->nullable();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->index('book_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_images');
    }
}
