<?php

namespace App\Http\Controllers\Web;

use App\Book;
use App\CartItem;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $items = CartItem::with('book.images')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('web.cart.index', compact('items'));
    }

    public function add(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 401, 'message' => '请先登录'], 401);
            }
            return redirect('/login');
        }

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::find($data['book_id']);
        if (!$book || $book->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json(['code' => 400, 'message' => '该书籍已下架或已售出'], 400);
            }
            return back()->with('error', '该书籍已下架或已售出');
        }

        $exists = CartItem::where('user_id', auth()->id())
            ->where('book_id', $data['book_id'])
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 400, 'message' => '已在购物车中'], 400);
            }
            return back()->with('error', '已在购物车中');
        }

        // 检查是否有软删除的旧记录，有则恢复
        $trashed = CartItem::withTrashed()
            ->where('user_id', auth()->id())
            ->where('book_id', $data['book_id'])
            ->first();

        if ($trashed) {
            $trashed->restore();
            if ($request->expectsJson()) {
                return response()->json(['code' => 200, 'message' => '已加入购物车']);
            }
            return back()->with('success', '已加入购物车');
        }

        CartItem::create([
            'user_id' => auth()->id(),
            'book_id' => $data['book_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['code' => 200, 'message' => '已加入购物车']);
        }
        return back()->with('success', '已加入购物车');
    }

    public function remove($id)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $item = CartItem::where('user_id', auth()->id())->findOrFail($id);
        $item->delete();

        return back()->with('success', '已移除');
    }

    public function clear()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        CartItem::where('user_id', auth()->id())->delete();

        return back()->with('success', '购物车已清空');
    }

    public function checkout(Request $request, OrderService $service)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $data = $request->validate([
            'book_ids' => 'required|array|min:1',
            'pickup_location' => 'required|string|max:100',
        ]);

        $order = $service->create(auth()->id(), $data['book_ids'], $data['pickup_location']);

        CartItem::where('user_id', auth()->id())
            ->whereIn('book_id', $data['book_ids'])
            ->delete();

        return redirect('/orders/' . $order->id)->with('success', '下单成功，请尽快付款');
    }
}
