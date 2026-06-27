<?php

namespace App\Http\Controllers\Api;

use App\Services\OrderService;
use App\Services\ReviewService;
use App\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request, OrderService $service)
    {
        $data = $request->validate([
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'exists:books,id',
            'pickup_location' => 'required|string|max:200',
        ]);

        // 不能购买自己卖的书
        foreach ($data['book_ids'] as $bookId) {
            $book = \App\Book::find($bookId);
            if ($book && $book->seller_id === auth()->id()) {
                return response()->json(['code' => 403, 'message' => '不能购买自己出售的书'], 403);
            }
        }

        $order = $service->create(auth()->id(), $data['book_ids'], $data['pickup_location']);

        return $this->success([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'total_amount' => $order->total_amount,
        ]);
    }

    public function index(Request $request)
    {
        $orders = Order::with('items.book.images')
            ->where(function ($q) {
                $q->where('buyer_id', auth()->id())
                  ->orWhereHas('items.book', function ($q) {
                      $q->where('seller_id', auth()->id());
                  });
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $list = $orders->map(function ($order) {
            return $this->formatOrder($order);
        });

        return $this->paginate($list, [
            'current_page' => $orders->currentPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
            'last_page' => $orders->lastPage(),
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['items.book.images', 'timeline'])->findOrFail($id);

        $isSeller = $order->items->contains(function ($item) {
            return $item->book && $item->book->seller_id === auth()->id();
        });
        if ($order->buyer_id !== auth()->id() && !$isSeller) {
            return $this->error(403, '无权查看此订单');
        }

        $data = $this->formatOrder($order);
        $data['is_buyer'] = $order->buyer_id === auth()->id();
        $data['is_seller'] = $isSeller;
        $data['timeline'] = $order->timeline->map(function ($t) {
            return [
                'status' => $t->status,
                'remark' => $t->remark,
                'created_at' => $t->created_at,
            ];
        });

        // 卖家信息
        if (in_array($order->status, ['paid', 'confirmed', 'picked_up'])) {
            $firstItem = $order->items->first();
            if ($firstItem && $firstItem->book && $firstItem->book->seller) {
                $data['seller'] = [
                    'name' => $firstItem->book->seller->name,
                    'phone' => $this->maskPhone($firstItem->book->seller->phone),
                ];
            }
        }

        // 评价（买家和卖家都可见）
        $review = \App\Review::where('order_id', $order->id)->first();
        if ($review) {
            $data['review'] = [
                'book_rating' => $review->book_rating,
                'service_rating' => $review->service_rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toDateTimeString(),
            ];
        }

        return $this->success($data);
    }

    public function pay($id, OrderService $service)
    {
        $order = Order::findOrFail($id);
        if ($order->buyer_id !== auth()->id()) {
            return $this->error(403, '无权操作此订单');
        }

        $service->pay($id);
        return $this->success(null, '支付成功');
    }

    public function cancel($id, Request $request, OrderService $service)
    {
        $order = Order::findOrFail($id);
        if ($order->buyer_id !== auth()->id()) {
            return $this->error(403, '无权操作此订单');
        }

        $service->cancel($id, $request->input('reason'));
        return $this->success(null, '订单已取消');
    }

    public function pickup($id, OrderService $service)
    {
        $order = Order::findOrFail($id);
        if ($order->buyer_id !== auth()->id()) {
            return $this->error(403, '无权操作此订单');
        }

        $service->pickup($id);
        return $this->success(null, '确认取书成功');
    }

    public function destroy($id)
    {
        $order = Order::with('items.book')->findOrFail($id);

        $isBuyer = $order->buyer_id === auth()->id();
        $isSeller = $order->items->contains(function ($item) {
            return $item->book && $item->book->seller_id === auth()->id();
        });

        if (!$isBuyer && !$isSeller) {
            return $this->error('无权操作此订单', 403);
        }

        $order->delete();
        return $this->success(null, '已删除');
    }

    public function review($id, Request $request, ReviewService $service)
    {
        $data = $request->validate([
            'book_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $service->review($id, auth()->id(), $data['book_rating'], $data['service_rating'], $data['comment'] ?? null);

        return $this->success(null, '评价成功');
    }

    private function formatOrder(Order $order): array
    {
        $cover = null;
        $books = $order->items->map(function ($item) use (&$cover) {
            $book = $item->book;
            $bookCover = null;
            if ($book) {
                $img = $book->images->where('type', 'cover')->first();
                $bookCover = $img ? '/storage/' . $img->path : null;
                if (!$cover) $cover = $bookCover;
            }
            return [
                'book_id' => $item->book_id,
                'title' => $book ? $book->title : '',
                'price' => $item->price,
                'cover_img' => $bookCover,
            ];
        });

        return [
            'id' => $order->id,
            'order_no' => $order->order_no,
            'status' => $order->status,
            'status_label' => $this->orderStatusLabel($order->status),
            'total_amount' => $order->total_amount,
            'pickup_location' => $order->pickup_location,
            'cover_img' => $cover,
            'books' => $books,
            'is_buyer' => $order->buyer_id === auth()->id(),
            'created_at' => $order->created_at->toDateTimeString(),
        ];
    }

    private function maskPhone(string $phone): string
    {
        return substr($phone, 0, 3) . '****' . substr($phone, 7);
    }

    private function orderStatusLabel(string $status): string
    {
        $map = [
            'pending' => '待付款',
            'paid' => '已付款',
            'confirmed' => '已确认',
            'picked_up' => '已完成',
            'cancelled' => '已取消',
        ];
        return $map[$status] ?? $status;
    }
}
