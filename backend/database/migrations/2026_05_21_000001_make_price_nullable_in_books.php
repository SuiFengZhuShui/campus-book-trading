<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakePriceNullableInBooks extends Migration
{
    public function up()
    {
        \DB::statement('ALTER TABLE `books` MODIFY `price` DECIMAL(10,2) NULL');
        \DB::statement('ALTER TABLE `books` MODIFY `cost_price` DECIMAL(10,2) NULL');
    }

    public function down()
    {
        \DB::statement('ALTER TABLE `books` MODIFY `price` DECIMAL(10,2) NOT NULL');
        \DB::statement('ALTER TABLE `books` MODIFY `cost_price` DECIMAL(10,2) NOT NULL');
    }
}
