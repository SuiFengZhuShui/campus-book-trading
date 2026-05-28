<?php

namespace App\Http\Controllers\Web;

use App\Book;
use App\Order;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $orders = Order::with(['items.book.images'])
            ->where(function ($q) {
                $q->where('buyer_id', auth()->id())
                  ->orWhereHas('items.book', function ($q) {
                      $q->where('seller_id', auth()->id());
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.orders.index', compact('orders'));
    }

    public function detail($id)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $order = Order::with(['items.book.images', 'items.book.category', 'timeline'])
            ->where(function ($q) {
                $q->where('buyer_id', auth()->id())
                  ->orWhereHas('items.book', function ($q) {
                      $q->where('seller_id', auth()->id());
                  });
            })
            ->findOrFail($id);

        return view('web.orders.detail', compact('order'));
    }

    public function buyForm($bookId)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $book = Book::with(['images', 'category'])
            ->where('status', 'active')
            ->findOrFail($bookId);

        return view('web.orders.buy', compact('book'));
    }

    public function buy($bookId, Request $request, OrderService $service)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $data = $request->validate([
            'pickup_location' => 'required|string|max:100',
        ]);

        $order = $service->create(auth()->id(), [$bookId], $data['pickup_location']);

        return redirect('/orders/' . $order->id)->with('success', '下单成功，请尽快付款');
    }

    public function pay($id, OrderService $service)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $order = Order::where('buyer_id', auth()->id())->findOrFail($id);
        $service->pay($order->id);

        return redirect('/orders/' . $order->id)->with('success', '支付成功');
    }

    public function cancel($id, OrderService $service)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $order = Order::where('buyer_id', auth()->id())->findOrFail($id);
        $service->cancel($order->id, '买家取消');

        return redirect('/orders/' . $order->id)->with('success', '订单已取消');
    }
}
