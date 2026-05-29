<?php

namespace App\Services;

use App\Book;
use App\Order;
use App\OrderItem;
use App\OrderTimeline;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(int $userId, array $bookIds, string $pickupLocation): Order
    {
        return DB::transaction(function () use ($userId, $bookIds, $pickupLocation) {
            $books = Book::whereIn('id', $bookIds)->where('status', 'active')->lockForUpdate()->get();

            if ($books->count() !== count($bookIds)) {
                $found = $books->pluck('id')->toArray();
                $missing = array_diff($bookIds, $found);
                $missingBook = Book::find($missing[0] ?? reset($missing));
                $title = $missingBook ? $missingBook->title : '某书籍';
                throw new BusinessException("《{$title}》已被他人购买，请重新下单");
            }

            $total = $books->sum('price');

            $order = new Order();
            $order->order_no = $this->generateOrderNo();
            $order->buyer_id = $userId;
            $order->total_amount = $total;
            $order->status = 'pending';
            $order->pickup_location = $pickupLocation;
            $order->save();

            foreach ($books as $book) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'price' => $book->price,
                ]);
            }

            OrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'remark' => '订单创建',
                'created_at' => now(),
            ]);

            return $order;
        });
    }

    public function pay(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'pending') {
            throw new BusinessException('当前订单状态不允许支付');
        }

        $order->status = 'paid';
        $order->paid_at = now();
        $order->save();

        OrderTimeline::create([
            'order_id' => $order->id,
            'status' => 'paid',
            'remark' => '买家已支付',
            'created_at' => now(),
        ]);
    }

    public function confirm(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'paid') {
            throw new BusinessException('当前订单状态不允许确认');
        }

        $order->status = 'confirmed';
        $order->confirmed_at = now();
        $order->save();

        OrderTimeline::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'remark' => '平台已确认',
            'created_at' => now(),
        ]);
    }

    public function cancel(int $orderId, ?string $reason): void
    {
        $order = Order::findOrFail($orderId);

        if (!in_array($order->status, ['pending', 'paid'])) {
            throw new BusinessException('当前订单状态不允许取消');
        }

        DB::transaction(function () use ($order, $reason) {
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->cancel_reason = $reason;
            $order->save();

            $bookIds = $order->items()->pluck('book_id')->toArray();
            Book::whereIn('id', $bookIds)->update(['status' => 'active']);

            OrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'remark' => $reason ?: '订单取消',
                'created_at' => now(),
            ]);
        });
    }

    public function pickup(int $orderId): void
    {
        $order = Order::with('items.book')->findOrFail($orderId);

        if ($order->status !== 'confirmed') {
            throw new BusinessException('当前订单状态不允许取书');
        }

        DB::transaction(function () use ($order) {
            $order->status = 'picked_up';
            $order->picked_up_at = now();
            $order->save();

            foreach ($order->items as $item) {
                $book = $item->book;
                if ($book) {
                    $book->seller_paid = 1;
                    $book->status = 'sold';
                    $book->save();
                }
            }

            OrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'picked_up',
                'remark' => '买家已取书，交易完成',
                'created_at' => now(),
            ]);
        });
    }

    public function cancelTimeoutOrders(): void
    {
        // 待付款超时 30 分钟
        $pendingOrders = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($pendingOrders as $order) {
            $this->cancel($order->id, '超时未支付');
        }

        // 已付款超时 48 小时
        $paidOrders = Order::where('status', 'paid')
            ->where('paid_at', '<', now()->subHours(48))
            ->get();

        foreach ($paidOrders as $order) {
            $this->cancel($order->id, '超时未确认');
        }
    }

    private function generateOrderNo(): string
    {
        return date('YmdHis') . sprintf('%04d', random_int(0, 9999));
    }
}
