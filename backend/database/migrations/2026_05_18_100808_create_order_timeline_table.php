<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTimelineTable extends Migration
{
    public function up()
    {
        Schema::create('order_timeline', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('订单');
            $table->string('status', 30)->comment('状态名');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamp('created_at')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_timeline');
    }
}
