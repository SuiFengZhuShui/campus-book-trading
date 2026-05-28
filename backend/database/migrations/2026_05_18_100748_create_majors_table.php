<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMajorsTable extends Migration
{
    public function up()
    {
        Schema::create('majors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('college_id')->comment('所属学院');
            $table->string('name', 100)->comment('专业名称');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();

            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('majors');
    }
}
