<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\CartItem;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with('book.images')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $list = $items->map(function ($item) {
            $book = $item->book;
            $cover = null;
            if ($book) {
                $img = $book->images->where('type', 'cover')->first();
                $cover = $img ? '/storage/' . $img->path : null;
            }
            return [
                'id' => $item->id,
                'book_id' => $book ? $book->id : null,
                'title' => $book ? $book->title : '',
                'author' => $book ? $book->author : '',
                'price' => $book ? $book->price : null,
                'cover_img' => $cover,
                'status' => $book ? $book->status : null,
            ];
        });

        return $this->success(['list' => $list]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::find($data['book_id']);
        if (!$book || $book->status !== 'active') {
            return $this->error(400, '该书籍已下架或已售出');
        }

        $exists = CartItem::where('user_id', auth()->id())
            ->where('book_id', $data['book_id'])
            ->exists();

        if ($exists) {
            return $this->error(400, '已在购物车中');
        }

        $item = CartItem::create([
            'user_id' => auth()->id(),
            'book_id' => $data['book_id'],
        ]);

        return $this->success(['id' => $item->id], '已加入购物车');
    }

    public function destroy($id)
    {
        $item = CartItem::where('user_id', auth()->id())->findOrFail($id);
        $item->delete();

        return $this->success(null, '已移除');
    }

    public function checkout(Request $request, OrderService $service)
    {
        $data = $request->validate([
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'exists:cart_items,book_id',
            'pickup_location' => 'required|string|max:200',
        ]);

        $order = $service->create(auth()->id(), $data['book_ids'], $data['pickup_location']);

        // 清空已下单的购物车项
        CartItem::where('user_id', auth()->id())
            ->whereIn('book_id', $data['book_ids'])
            ->delete();

        return $this->success([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'total_amount' => $order->total_amount,
        ]);
    }
}
