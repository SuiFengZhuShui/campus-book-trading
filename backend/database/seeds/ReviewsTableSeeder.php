<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        $picked_upOrders = DB::table('orders')->where('status', 'picked_up')->get();

        $reviews = [
            ['book_rating' => 5, 'service_rating' => 5, 'comment' => '书特别新，跟描述一致，取书也很方便！'],
            ['book_rating' => 4, 'service_rating' => 5, 'comment' => '书有点笔记但不影响用，价格实惠'],
            ['book_rating' => 5, 'service_rating' => 4, 'comment' => '书很好，就是等确认时间长了一点'],
            ['book_rating' => 3, 'service_rating' => 4, 'comment' => '比想象中旧一点，但卖家描述算准确'],
            ['book_rating' => 5, 'service_rating' => 5, 'comment' => '特别满意！几乎新书，学长人也很nice'],
        ];

        foreach ($picked_upOrders as $i => $order) {
            if ($i >= count($reviews)) break;

            $orderItem = DB::table('order_items')->where('order_id', $order->id)->first();
            if (!$orderItem) continue;

            DB::table('reviews')->insert([
                'order_id' => $order->id,
                'user_id' => $order->buyer_id,
                'book_id' => $orderItem->book_id,
                'book_rating' => $reviews[$i]['book_rating'],
                'service_rating' => $reviews[$i]['service_rating'],
                'comment' => $reviews[$i]['comment'],
                'created_at' => now()->subDays(rand(0, 2)),
                'updated_at' => now(),
            ]);
        }
    }
}
