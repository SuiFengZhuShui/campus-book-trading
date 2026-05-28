<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 200)->comment('书名');
            $table->string('author', 100)->comment('作者');
            $table->string('publisher', 100)->comment('出版社');
            $table->string('isbn', 20)->nullable()->comment('ISBN');
            $table->unsignedBigInteger('category_id')->comment('分类');
            $table->unsignedBigInteger('course_id')->nullable()->comment('关联课程');
            $table->enum('condition', ['like_new', 'excellent', 'good', 'fair'])->comment('成色');
            $table->decimal('original_price', 10, 2)->comment('原价');
            $table->decimal('price', 10, 2)->comment('售价');
            $table->decimal('cost_price', 10, 2)->comment('收书价');
            $table->unsignedBigInteger('seller_id')->comment('卖书学生');
            $table->enum('status', ['pending_review', 'approved', 'active', 'sold', 'removed'])->default('pending_review')->comment('状态');
            $table->timestamp('submitted_at')->nullable()->comment('提交时间');
            $table->timestamp('approved_at')->nullable()->comment('审核通过时间');
            $table->timestamp('received_at')->nullable()->comment('入库时间');
            $table->tinyInteger('seller_paid')->default(0)->comment('卖家结算 0=未结算 1=已结算');
            $table->text('description')->nullable()->comment('补充说明');
            $table->string('reject_reason', 255)->nullable()->comment('驳回原因');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('restrict');
            $table->index('title');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
