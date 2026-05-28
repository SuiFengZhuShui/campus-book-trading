<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_id', 20)->nullable()->unique()->comment('学号');
            $table->string('name', 50)->comment('真实姓名');
            $table->string('phone', 11)->unique()->comment('手机号');
            $table->string('password')->comment('bcrypt');
            $table->enum('role', ['student', 'admin'])->default('student')->comment('角色');
            $table->string('avatar', 255)->nullable()->comment('头像');
            $table->tinyInteger('status')->default(1)->comment('1=正常 0=禁用');
            $table->string('api_token', 80)->unique()->nullable()->comment('API token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
