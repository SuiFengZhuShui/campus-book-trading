<?php

namespace App\Services;

use App\Order;
use App\Review;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function review(int $orderId, int $userId, int $bookRating, int $serviceRating, ?string $comment): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->buyer_id !== $userId) {
            throw new BusinessException('仅买家可评价', 403);
        }

        if ($order->status !== 'picked_up') {
            throw new BusinessException('当前订单状态不允许评价');
        }

        if (Review::where('order_id', $orderId)->exists()) {
            throw new BusinessException('该订单已评价');
        }

        DB::transaction(function () use ($order, $userId, $bookRating, $serviceRating, $comment) {
            $bookId = $order->items()->first()->book_id;

            Review::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'book_id' => $bookId,
                'book_rating' => $bookRating,
                'service_rating' => $serviceRating,
                'comment' => $comment,
            ]);
        });
    }
}
