<?php

namespace App\Http\Controllers\Api;

use App\Want;
use App\Services\WantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WantController extends Controller
{
    public function index(Request $request)
    {
        $wants = Want::with(['user', 'category'])
            ->when($request->keyword, function ($q, $v) {
                $q->where('title', 'like', "%{$v}%");
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $list = $wants->map(function ($w) {
            return [
                'id' => $w->id,
                'title' => $w->title,
                'author' => $w->author,
                'publisher' => $w->publisher,
                'category_id' => $w->category_id,
                'category_name' => $w->category ? $w->category->name : null,
                'max_price' => $w->max_price,
                'acceptable_condition' => $w->acceptable_condition,
                'condition_label' => $this->conditionLabel($w->acceptable_condition),
                'status' => $w->status,
                'status_label' => $this->wantStatusLabel($w->status),
                'fulfiller_count' => $w->fulfillments()->count(),
                'expires_at' => $w->expires_at->toDateTimeString(),
                'created_at' => $w->created_at->toDateTimeString(),
            ];
        });

        return $this->paginate($list, [
            'current_page' => $wants->currentPage(),
            'per_page' => $wants->perPage(),
            'total' => $wants->total(),
            'last_page' => $wants->lastPage(),
        ]);
    }

    public function show($id)
    {
        $want = Want::with(['user', 'category', 'fulfillments.fulfiller', 'fulfillments.book'])->findOrFail($id);

        $data = [
            'id' => $want->id,
            'title' => $want->title,
            'author' => $want->author,
            'publisher' => $want->publisher,
            'category_id' => $want->category_id,
            'category_name' => $want->category ? $want->category->name : null,
            'max_price' => $want->max_price,
            'acceptable_condition' => $want->acceptable_condition,
            'condition_label' => $this->conditionLabel($want->acceptable_condition),
            'status' => $want->status,
            'status_label' => $this->wantStatusLabel($want->status),
            'expires_at' => $want->expires_at->toDateTimeString(),
            'created_at' => $want->created_at->toDateTimeString(),
            'fulfillments' => $want->fulfillments->map(function ($f) {
                return [
                    'id' => $f->id,
                    'fulfiller_name' => $f->fulfiller ? $f->fulfiller->name : '匿名',
                    'status' => $f->status,
                    'status_label' => $this->fulfillStatusLabel($f->status),
                    'book_id' => $f->book_id,
                    'book_title' => $f->book ? $f->book->title : null,
                    'created_at' => $f->created_at->toDateTimeString(),
                ];
            }),
        ];

        // 仅求购满足后对发布者展示接单人信息
        if ($want->status === 'fulfilled' && auth()->id() === $want->user_id) {
            $data['fulfiller_visible'] = true;
        }

        // 发布者信息脱敏
        $data['publisher_name'] = $want->user ? $want->user->name : '匿名';

        return $this->success($data);
    }

    public function store(Request $request, WantService $service)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'author' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:100',
            'category_id' => 'nullable|integer|exists:categories,id',
            'max_price' => 'required|numeric|min:0.01',
            'acceptable_condition' => 'nullable|string|max:50',
        ]);

        $activeBook = \App\Book::where('title', 'like', $data['title'])
            ->where('status', 'active')
            ->first();
        if ($activeBook) {
            return response()->json([
                'code' => 422,
                'message' => '该书已有用户在售（¥' . $activeBook->price . '），可直接购买',
                'data' => ['book_id' => $activeBook->id],
            ], 422);
        }

        $want = $service->create($data);

        return $this->success(['id' => $want->id], '发布成功');
    }

    public function fulfill($id, WantService $service)
    {
        $service->fulfill($id, auth()->id());

        return $this->success(null, '接单成功，请联系平台走正常卖书流程');
    }

    private function wantStatusLabel(string $status): string
    {
        $map = [
            'active' => '进行中',
            'fulfilled' => '已满足',
            'expired' => '已过期',
            'closed' => '已关闭',
        ];
        return $map[$status] ?? $status;
    }

    private function conditionLabel(string $condition): string
    {
        $map = [
            'like_new' => '全新',
            'excellent' => '几乎全新',
            'good' => '正常使用',
            'fair' => '较旧',
        ];
        $parts = array_map(function ($v) use ($map) {
            return $map[trim($v)] ?? trim($v);
        }, explode(',', $condition));
        return implode('、', $parts);
    }

    private function fulfillStatusLabel(string $status): string
    {
        $map = [
            'pending' => '等待提交',
            'listed' => '已上架',
            'completed' => '已完成',
        ];
        return $map[$status] ?? $status;
    }
}
