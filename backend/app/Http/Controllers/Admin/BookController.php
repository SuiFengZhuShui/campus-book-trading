<?php

namespace App\Http\Controllers\Admin;

use App\Book;
use App\Services\BookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return redirect(url('admin/books'))->with('success', '已删除');
    }

    public function index(Request $request)
    {
        $query = Book::with('seller', 'images', 'category')
            ->when($request->status, function ($q, $v) {
                if ($v === 'all') return;
                $q->where('status', $v);
            })
            ->when($request->category_id, function ($q, $v) {
                $q->where('category_id', $v);
            })
            ->when($request->keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('title', 'like', "%{$v}%")
                      ->orWhere('author', 'like', "%{$v}%")
                      ->orWhere('isbn', 'like', "%{$v}%");
                });
            })
            ->orderBy('created_at', 'desc');

        $books = $query->paginate(15);

        return view('admin.books.index', compact('books'));
    }

    public function edit($id)
    {
        $book = Book::with('images', 'seller')->findOrFail($id);
        return view('admin.books.edit', compact('book'));
    }

    public function update($id, Request $request, BookService $service)
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

        $service->update($id, $data);

        return back()->with('page_success', '保存成功');
    }

    public function remove($id, BookService $service)
    {
        $service->remove($id);
        return redirect()->route('admin.books.index')->with('success', '已下架');
    }
}
