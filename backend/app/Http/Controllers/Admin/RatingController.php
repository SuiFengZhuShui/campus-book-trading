<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function index()
    {
        $reviews = DB::table('reviews')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->leftJoin('books', 'reviews.book_id', '=', 'books.id')
            ->whereNull('reviews.deleted_at')
            ->select('reviews.*', 'users.name as user_name', 'books.title as book_title')
            ->orderBy('reviews.created_at', 'desc')
            ->paginate(15);

        return view('admin.ratings.index', compact('reviews'));
    }

    public function show($id)
    {
        $review = DB::table('reviews')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->leftJoin('books', 'reviews.book_id', '=', 'books.id')
            ->leftJoin('orders', 'reviews.order_id', '=', 'orders.id')
            ->leftJoin('book_images', function ($join) {
                $join->on('books.id', '=', 'book_images.book_id')
                     ->where('book_images.type', '=', 'cover');
            })
            ->where('reviews.id', $id)
            ->whereNull('reviews.deleted_at')
            ->select(
                'reviews.*',
                'users.name as user_name',
                'books.title as book_title',
                'books.author as book_author',
                'books.publisher as book_publisher',
                'book_images.path as cover_path',
                'orders.order_no as order_no',
                'orders.status as order_status',
                'orders.total_amount as order_amount'
            )
            ->first();

        if (!$review) {
            abort(404);
        }

        return view('admin.ratings.detail', compact('review'));
    }
}
