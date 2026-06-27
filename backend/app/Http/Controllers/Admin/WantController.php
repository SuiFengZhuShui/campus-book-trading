<?php

namespace App\Http\Controllers\Admin;

use App\Want;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WantController extends Controller
{
    public function index(Request $request)
    {
        $query = Want::with(['user', 'category'])
            ->withCount('fulfillments')
            ->when($request->status, function ($q, $v) {
                if ($v === 'all') return;
                $q->where('status', $v);
            })
            ->when($request->keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('title', 'like', "%{$v}%")
                      ->orWhereHas('user', function ($q) use ($v) {
                          $q->where('name', 'like', "%{$v}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc');

        $wants = $query->paginate(15);

        return view('admin.wants.index', compact('wants'));
    }

    public function edit($id)
    {
        $want = Want::findOrFail($id);
        $categories = \App\Category::orderBy('sort')->get();
        return view('admin.wants.edit', compact('want', 'categories'));
    }

    public function update($id, Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'author' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'max_price' => 'required|numeric|min:0',
            'acceptable_condition' => 'nullable|array',
            'acceptable_condition.*' => 'string',
            'status' => 'required|in:active,expired,closed',
        ]);

        $want = Want::findOrFail($id);
        if (isset($data['acceptable_condition'])) {
            $data['acceptable_condition'] = implode(',', $data['acceptable_condition']);
        }
        $want->fill($data)->save();

        return back()->with('page_success', '求购已更新');
    }

    public function destroy($id)
    {
        $want = Want::findOrFail($id);
        $want->fulfillments()->delete();
        $want->delete();

        return back()->with('success', '求购已删除');
    }
}
