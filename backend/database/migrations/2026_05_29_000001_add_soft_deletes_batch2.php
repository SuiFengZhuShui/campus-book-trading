<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesBatch2 extends Migration
{
    public function up()
    {
        Schema::table('wants', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('want_fulfillments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('book_images', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('order_timeline', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('wants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('want_fulfillments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('book_images', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('order_timeline', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
