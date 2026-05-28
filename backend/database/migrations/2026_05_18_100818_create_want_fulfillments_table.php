<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWantFulfillmentsTable extends Migration
{
    public function up()
    {
        Schema::create('want_fulfillments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('want_id')->comment('求购');
            $table->unsignedBigInteger('fulfiller_id')->comment('接单人');
            $table->unsignedBigInteger('book_id')->nullable()->comment('关联书籍（上架后关联）');
            $table->enum('status', ['pending', 'listed', 'completed'])->default('pending')->comment('状态');
            $table->timestamps();

            $table->foreign('want_id')->references('id')->on('wants')->onDelete('cascade');
            $table->foreign('fulfiller_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('want_fulfillments');
    }
}
