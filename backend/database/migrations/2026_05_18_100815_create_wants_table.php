<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWantsTable extends Migration
{
    public function up()
    {
        Schema::create('wants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('发布者');
            $table->string('title', 200)->comment('求购书名');
            $table->string('author', 100)->nullable()->comment('作者');
            $table->string('publisher', 100)->nullable()->comment('出版社');
            $table->decimal('max_price', 10, 2)->comment('最高接受价');
            $table->string('acceptable_condition', 50)->nullable()->comment('可接受成色，逗号分隔');
            $table->enum('status', ['active', 'fulfilled', 'expired', 'closed'])->default('active')->comment('状态');
            $table->timestamp('expires_at')->comment('过期时间');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wants');
    }
}
