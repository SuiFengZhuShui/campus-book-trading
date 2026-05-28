<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdToWants extends Migration
{
    public function up()
    {
        Schema::table('wants', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('user_id')->comment('学院分类');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('wants', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
