<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUsersUniqueIndexes extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_unique');
            $table->dropUnique('users_student_id_unique');

            $table->unique(['phone', 'deleted_at'], 'users_phone_deleted_at_unique');
            $table->unique(['student_id', 'deleted_at'], 'users_student_id_deleted_at_unique');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_deleted_at_unique');
            $table->dropUnique('users_student_id_deleted_at_unique');

            $table->unique('phone', 'users_phone_unique');
            $table->unique('student_id', 'users_student_id_unique');
        });
    }
}
