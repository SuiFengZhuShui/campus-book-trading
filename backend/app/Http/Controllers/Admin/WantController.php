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

    public function destroy($id)
    {
        $want = Want::findOrFail($id);
        $want->fulfillments()->delete();
        $want->delete();

        return back()->with('success', '求购已删除');
    }
}
