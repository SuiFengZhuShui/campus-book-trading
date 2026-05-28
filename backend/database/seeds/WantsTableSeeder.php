<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WantsTableSeeder extends Seeder
{
    public function run()
    {
        $userIds = DB::table('users')->where('role', 'student')->pluck('id')->toArray();
        if (empty($userIds)) return;

        $wants = [
            [
                'title' => '高等数学（同济第七版）上册',
                'author' => '同济大学数学系',
                'publisher' => '高等教育出版社',
                'max_price' => 20.00,
                'acceptable_condition' => 'excellent,good',
                'category_id' => 1,
            ],
            [
                'title' => '大学英语精读第三版第1册',
                'author' => '董亚芬',
                'publisher' => '上海外语教育出版社',
                'max_price' => 15.00,
                'acceptable_condition' => 'like_new,excellent,good,fair',
                'category_id' => 1,
            ],
            [
                'title' => '计算机组成原理（第3版）',
                'author' => '唐朔飞',
                'publisher' => '高等教育出版社',
                'max_price' => 25.00,
                'acceptable_condition' => 'excellent,good',
                'category_id' => 8,
            ],
            [
                'title' => '电路分析基础（第5版）',
                'author' => '李瀚荪',
                'publisher' => '高等教育出版社',
                'max_price' => 22.00,
                'acceptable_condition' => 'excellent,good,fair',
                'category_id' => 1,
            ],
            [
                'title' => '经济学原理（第8版）微观分册',
                'author' => '曼昆',
                'publisher' => '北京大学出版社',
                'max_price' => 35.00,
                'acceptable_condition' => 'excellent,good',
                'category_id' => 3,
            ],
            [
                'title' => '统计学（第7版）',
                'author' => '贾俊平',
                'publisher' => '中国人民大学出版社',
                'max_price' => 28.00,
                'acceptable_condition' => 'like_new,excellent,good',
                'category_id' => 3,
            ],
            [
                'title' => '毛泽东思想与中国特色社会主义理论体系概论',
                'author' => '本书编写组',
                'publisher' => '高等教育出版社',
                'max_price' => 15.00,
                'acceptable_condition' => 'excellent,good,fair',
                'category_id' => 1,
            ],
            [
                'title' => '单片机原理与应用（第4版）',
                'author' => '张毅刚',
                'publisher' => '高等教育出版社',
                'max_price' => 30.00,
                'acceptable_condition' => 'excellent,good',
                'category_id' => 2,
            ],
            [
                'title' => '自动控制原理（第6版）',
                'author' => '胡寿松',
                'publisher' => '科学出版社',
                'max_price' => 28.00,
                'acceptable_condition' => 'excellent,good',
                'category_id' => 2,
            ],
            [
                'title' => 'Linux命令行与shell脚本编程大全（第3版）',
                'author' => 'Richard Blum',
                'publisher' => '人民邮电出版社',
                'max_price' => 40.00,
                'acceptable_condition' => 'like_new,excellent',
                'category_id' => 8,
            ],
            [
                'title' => '人力资源管理（第12版）',
                'author' => '加里·德斯勒',
                'publisher' => '中国人民大学出版社',
                'max_price' => 35.00,
                'acceptable_condition' => 'excellent,good',
                'category_id' => 3,
            ],
            [
                'title' => '电子商务概论（第5版）',
                'author' => '张润彤',
                'publisher' => '电子工业出版社',
                'max_price' => 18.00,
                'acceptable_condition' => 'excellent,good,fair',
                'category_id' => 6,
            ],
        ];

        foreach ($wants as $i => $data) {
            DB::table('wants')->insert([
                'user_id' => $userIds[$i % count($userIds)],
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'max_price' => $data['max_price'],
                'acceptable_condition' => $data['acceptable_condition'],
                'status' => 'active',
                'expires_at' => now()->addDays(rand(7, 30)),
                'created_at' => now()->subDays(rand(0, 7)),
                'updated_at' => now(),
            ]);
        }
    }
}
