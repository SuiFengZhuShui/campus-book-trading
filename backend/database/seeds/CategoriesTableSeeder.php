<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            '电子信息工程学院',
            '机电工程学院',
            '财经与物流管理学院',
            '环境与食品学院',
            '汽车工程学院',
            '贸易与旅游学院',
            '艺术学院',
        ];

        foreach ($categories as $i => $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'sort' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
