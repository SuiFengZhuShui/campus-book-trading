<?php

namespace App\Http\Controllers\Admin;

use App\Book;
use App\Order;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingReview = Book::where('status', 'pending_review')->count();
        $activeBooks = Book::where('status', 'active')->count();
        $pendingOrders = Order::where('status', 'paid')->count();
        $todayOrders = Order::whereDate('created_at', today())->count();

        $recentBooks = Book::where('status', 'pending_review')
            ->with('seller')
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::where('status', 'paid')
            ->with('buyer')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingReview', 'activeBooks', 'pendingOrders', 'todayOrders',
            'recentBooks', 'recentOrders'
        ));
    }
}
