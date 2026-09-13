<?php

namespace App\Http\Controllers\Admin;

use App\Book;
use App\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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

        $start = now()->subDays(29)->startOfDay();

        $submitRows = Book::where('submitted_at', '>=', $start)
            ->selectRaw('DATE(submitted_at) AS d, COUNT(*) AS c')
            ->groupBy(DB::raw('DATE(submitted_at)'))
            ->pluck('c', 'd')->toArray();
        $approveRows = Book::where('approved_at', '>=', $start)
            ->selectRaw('DATE(approved_at) AS d, COUNT(*) AS c')
            ->groupBy(DB::raw('DATE(approved_at)'))
            ->pluck('c', 'd')->toArray();
        $paidRows = Order::where('paid_at', '>=', $start)
            ->selectRaw('DATE(paid_at) AS d, COUNT(*) AS c')
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->pluck('c', 'd')->toArray();
        $orderRows = Order::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) AS d, COUNT(*) AS c')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('c', 'd')->toArray();

        $dates = [];
        $submit = [];
        $approve = [];
        $paid = [];
        $order = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $dates[] = $d;
            $submit[] = $submitRows[$d] ?? 0;
            $approve[] = $approveRows[$d] ?? 0;
            $paid[] = $paidRows[$d] ?? 0;
            $order[] = $orderRows[$d] ?? 0;
        }

        return view('admin.dashboard', array_merge(compact(
            'pendingReview', 'activeBooks', 'pendingOrders', 'todayOrders',
            'recentBooks', 'recentOrders'
        ), [
            'submitTrend' => ['dates' => $dates, 'values' => $submit],
            'approveTrend' => ['dates' => $dates, 'values' => $approve],
            'paidTrend' => ['dates' => $dates, 'values' => $paid],
            'orderTrend' => ['dates' => $dates, 'values' => $order],
        ]));
    }
}
