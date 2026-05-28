<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('buyer', 'items.book')
            ->when($request->status, function ($q, $v) {
                if ($v === 'all') return;
                $q->where('status', $v);
            })
            ->when($request->keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('order_no', 'like', "%{$v}%")
                      ->orWhereHas('buyer', function ($q) use ($v) {
                          $q->where('name', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc');

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function detail($id)
    {
        $order = Order::with('buyer', 'items.book.images', 'items.book.seller', 'timeline')->findOrFail($id);

        return view('admin.orders.detail', compact('order'));
    }

    public function confirm($id, Request $request, OrderService $service)
    {
        $service->confirm($id);

        return redirect()->route('admin.orders.index')->with('success', '订单已确认');
    }

    public function pickup($id, OrderService $service)
    {
        $service->pickup($id);

        return redirect()->route('admin.orders.index')->with('success', '已确认取书，卖家已结算');
    }

    public function cancel($id, Request $request, OrderService $service)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $service->cancel($id, $data['reason']);

        return redirect()->route('admin.orders.index')->with('success', '订单已取消并退款');
    }
}
