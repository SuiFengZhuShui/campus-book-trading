<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no', 32)->unique()->comment('订单号');
            $table->unsignedBigInteger('buyer_id')->comment('买家');
            $table->decimal('total_amount', 10, 2)->comment('订单金额');
            $table->enum('status', ['pending', 'paid', 'confirmed', 'picked_up', 'completed', 'cancelled'])->default('pending')->comment('状态');
            $table->string('pickup_location', 200)->comment('取书地点');
            $table->timestamp('paid_at')->nullable()->comment('支付时间');
            $table->timestamp('confirmed_at')->nullable()->comment('确认时间');
            $table->timestamp('picked_up_at')->nullable()->comment('取书时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('cancelled_at')->nullable()->comment('取消时间');
            $table->string('cancel_reason', 255)->nullable()->comment('取消原因');
            $table->timestamps();

            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('restrict');
            $table->index('order_no');
            $table->index('status');
            $table->index('buyer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
