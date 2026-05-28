<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('订单');
            $table->unsignedBigInteger('book_id')->comment('书籍');
            $table->decimal('price', 10, 2)->comment('下单时价格快照');
            $table->timestamp('created_at')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('restrict');
            $table->index('order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
