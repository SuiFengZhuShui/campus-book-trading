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
        $data['timeline'] = $order->timeline->map(function ($t) {
            return [
                'status' => $t->status,
                'remark' => $t->remark,
                'created_at' => $t->created_at,
            ];
        });

        // 卖家信息（仅购买者可见）
        if (in_array($order->status, ['paid', 'confirmed', 'picked_up'])) {
            $firstItem = $order->items->first();
            if ($firstItem && $firstItem->book && $firstItem->book->seller) {
                $data['seller'] = [
                    'name' => $firstItem->book->seller->name,
                    'phone' => $this->maskPhone($firstItem->book->seller->phone),
                ];
            }
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
            if (!$cover && $book) {
                $img = $book->images->where('type', 'cover')->first();
                $cover = $img ? asset('storage/' . $img->path) : null;
            }
            return [
                'book_id' => $item->book_id,
                'title' => $book ? $book->title : '',
                'price' => $item->price,
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
