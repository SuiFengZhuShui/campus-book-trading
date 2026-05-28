<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $activeBooks = DB::table('books')->where('status', 'active')->pluck('price', 'id')->toArray();
        $studentIds = DB::table('users')->where('role', 'student')->pluck('id')->toArray();

        if (empty($activeBooks) || count($studentIds) < 2) return;

        $bookIds = array_keys($activeBooks);
        $locations = ['图书馆门口', '第二食堂门口', '第三教学楼大厅', '宿舍楼A区快递点', '行政楼一楼'];

        $orderStatuses = [
            ['status' => 'picked_up', 'desc' => '已完成的订单'],
            ['status' => 'picked_up', 'desc' => '已完成的订单'],
            ['status' => 'picked_up', 'desc' => '已完成的订单'],
            ['status' => 'picked_up', 'desc' => '已完成的订单'],
            ['status' => 'picked_up', 'desc' => '已完成的订单'],
            ['status' => 'paid', 'desc' => '已支付待确认'],
            ['status' => 'confirmed', 'desc' => '已确认待取书'],
            ['status' => 'pending', 'desc' => '待支付'],
            ['status' => 'picked_up', 'desc' => '已取书待评价'],
            ['status' => 'cancelled', 'desc' => '已取消'],
        ];

        foreach ($orderStatuses as $i => $o) {
            // 确保买家不是卖家自己
            $bookId = $bookIds[$i % count($bookIds)];
            $sellerId = DB::table('books')->where('id', $bookId)->value('seller_id');
            $buyerCandidates = array_diff($studentIds, [$sellerId]);
            if (empty($buyerCandidates)) continue;
            $buyerId = $buyerCandidates[array_rand($buyerCandidates)];

            $orderNo = 'ORD' . date('Ymd') . str_pad($i + 1, 6, '0', STR_PAD_LEFT);
            $bookPrice = $activeBooks[$bookId];

            $orderId = DB::table('orders')->insertGetId([
                'order_no' => $orderNo,
                'buyer_id' => $buyerId,
                'total_amount' => $bookPrice,
                'status' => $o['status'],
                'pickup_location' => $locations[$i % count($locations)],
                'paid_at' => in_array($o['status'], ['paid', 'confirmed', 'picked_up', 'picked_up']) ? now()->subDays(rand(5, 14)) : null,
                'confirmed_at' => in_array($o['status'], ['confirmed', 'picked_up', 'picked_up']) ? now()->subDays(rand(3, 10)) : null,
                'picked_up_at' => in_array($o['status'], ['picked_up', 'picked_up']) ? now()->subDays(rand(1, 7)) : null,
                'completed_at' => $o['status'] === 'picked_up' ? now()->subDays(rand(0, 3)) : null,
                'cancelled_at' => $o['status'] === 'cancelled' ? now()->subDays(1) : null,
                'cancel_reason' => $o['status'] === 'cancelled' ? '临时不需要了' : null,
                'created_at' => now()->subDays(rand(5, 20)),
                'updated_at' => now(),
            ]);

            // order_items
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'book_id' => $bookId,
                'price' => $bookPrice,
                'created_at' => now(),
            ]);

            // order_timeline
            $this->createTimeline($orderId, $o['status']);

            // 更新书的状态
            if (in_array($o['status'], ['paid', 'confirmed', 'picked_up', 'picked_up'])) {
                DB::table('books')->where('id', $bookId)->update(['status' => 'sold']);
            }
        }
    }

    private function createTimeline($orderId, $status)
    {
        $steps = ['pending'];

        if (in_array($status, ['paid', 'confirmed', 'picked_up', 'picked_up'])) {
            $steps[] = 'paid';
        }
        if (in_array($status, ['confirmed', 'picked_up', 'picked_up'])) {
            $steps[] = 'confirmed';
        }
        if (in_array($status, ['picked_up', 'picked_up'])) {
            $steps[] = 'picked_up';
        }
        if ($status === 'picked_up') {
            $steps[] = 'picked_up';
        }
        if ($status === 'cancelled') {
            $steps[] = 'cancelled';
        }

        foreach ($steps as $j => $step) {
            DB::table('order_timeline')->insert([
                'order_id' => $orderId,
                'status' => $step,
                'remark' => $this->timelineRemark($step),
                'created_at' => now()->subDays(count($steps) - $j),
            ]);
        }
    }

    private function timelineRemark($step)
    {
        $map = [
            'pending' => '订单已提交',
            'paid' => '买家已付款',
            'confirmed' => '平台已确认',
            'picked_up' => '买家已取书',
            'picked_up' => '交易完成',
            'cancelled' => '订单已取消',
        ];
        return $map[$step] ?? '';
    }
}
