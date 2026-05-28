<?php

namespace App\Http\Controllers\Admin;

use App\Book;
use App\Services\BookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('seller', 'images')
            ->when($request->status, function ($q, $v) {
                if ($v === 'all') return;
                $q->where('status', $v);
            }, function ($q) {
                $q->where('status', 'pending_review');
            })
            ->when($request->keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('title', 'like', "%{$v}%")
                      ->orWhere('author', 'like', "%{$v}%");
                });
            })
            ->orderBy('submitted_at', 'desc');

        $books = $query->paginate(15);

        return view('admin.reviews.index', compact('books'));
    }

    public function detail($id)
    {
        $book = Book::with('seller', 'images', 'category', 'course.major.college')->findOrFail($id);
        $suggestPrice = app(BookService::class)->suggestPrice(
            $book->title, $book->author, $book->publisher, $book->original_price
        );

        return view('admin.reviews.detail', compact('book', 'suggestPrice'));
    }

    public function approve($id, Request $request, BookService $service)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'author' => 'required|string|max:100',
            'publisher' => 'required|string|max:100',
            'isbn' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'course_id' => 'nullable|exists:courses,id',
            'condition' => 'required|in:like_new,excellent,good,fair',
            'original_price' => 'required|numeric|min:0.01',
            'price' => 'required|numeric|min:0.01',
            'cost_price' => 'required|numeric|min:0',
        ]);

        $service->approve($id, $data);

        return redirect()->route('admin.reviews.index')->with('success', '审核通过');
    }

    public function reject($id, Request $request, BookService $service)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $service->reject($id, $data['reason']);

        return redirect()->route('admin.reviews.index')->with('success', '已驳回');
    }

    public function receive($id, Request $request, BookService $service)
    {
        $price = $request->input('price');
        $costPrice = $request->input('cost_price');

        $service->receive($id, $price ? (float) $price : null, $costPrice ? (float) $costPrice : null);

        return redirect()->route('admin.reviews.index')->with('success', '已入库上架');
    }
}
