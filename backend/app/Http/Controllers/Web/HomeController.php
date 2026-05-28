<?php

namespace App\Http\Controllers\Web;

use App\Book;
use App\Category;
use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categoryId = request('category_id');
        $sort = request('sort');

        $books = Book::with(['images', 'category'])
            ->where('status', 'active')
            ->when($categoryId, function ($q, $v) {
                $q->where('category_id', $v);
            })
            ->when($sort, function ($q, $v) {
                if ($v === 'price_asc') $q->orderBy('price', 'asc');
                elseif ($v === 'price_desc') $q->orderBy('price', 'desc');
                elseif ($v === 'condition') $q->orderByRaw("FIELD(books.condition, 'like_new','excellent','good','fair')");
                else $q->orderBy('received_at', 'desc');
            }, function ($q) {
                $q->orderBy('received_at', 'desc');
            })
            ->paginate(12);

        $categories = Category::orderBy('sort')->get();

        return view('web.home', compact('books', 'categories'));
    }

    public function search()
    {
        $keyword = request('keyword');
        $categoryId = request('category_id');
        $sort = request('sort');

        $books = Book::with(['images', 'category'])
            ->where('status', 'active')
            ->when($keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('title', 'like', "%{$v}%")
                      ->orWhere('author', 'like', "%{$v}%");
                });
            })
            ->when($categoryId, function ($q, $v) {
                $q->where('category_id', $v);
            })
            ->when($sort, function ($q, $v) {
                if ($v === 'price_asc') $q->orderBy('price', 'asc');
                elseif ($v === 'price_desc') $q->orderBy('price', 'desc');
                elseif ($v === 'condition') $q->orderByRaw("FIELD(books.condition, 'like_new','excellent','good','fair')");
                else $q->orderBy('received_at', 'desc');
            }, function ($q) {
                $q->orderBy('received_at', 'desc');
            })
            ->paginate(12);

        $categories = Category::orderBy('sort')->get();

        return view('web.home', compact('books', 'categories'));
    }

    public function detail($id)
    {
        $book = Book::with(['images', 'category', 'seller'])->findOrFail($id);

        // 非在售书籍仅卖家和管理员可查看
        if ($book->status !== 'active') {
            $user = auth()->user();
            if (!$user || ($user->id !== $book->seller_id && $user->role !== 'admin')) {
                abort(404);
            }
        }

        return view('web.book-detail', compact('book'));
    }

    public function mySells()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $books = Book::with(['images', 'category', 'orderItems.order'])
            ->where('seller_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.my-sells', compact('books'));
    }

    public function wants()
    {
        $wants = \App\Want::with(['user', 'category'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('web.wants', compact('wants'));
    }

    public function wantDetail($id)
    {
        $want = \App\Want::with(['user', 'category'])->findOrFail($id);
        return view('web.want-detail', compact('want'));
    }

    public function postWant()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $categories = Category::orderBy('sort')->get();
        return view('web.post-want', compact('categories'));
    }

    public function storeWant(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 401, 'message' => '请先登录'], 401);
            }
            return redirect('/login');
        }

        $conditions = $request->input('acceptable_condition', []);
        if (is_array($conditions)) {
            $conditions = implode(',', array_filter($conditions));
            $request->merge(['acceptable_condition' => $conditions ?: null]);
        }

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
            $msg = '该书已有用户在售（¥' . $activeBook->price . '），可前往<a href="/books/' . $activeBook->id . '" style="color:#2c5282;font-weight:600;text-decoration:underline;">书籍详情</a>直接购买';
            if ($request->expectsJson()) {
                return response()->json(['code' => 422, 'message' => strip_tags($msg)], 422);
            }
            return back()->withInput()->with('warning', $msg);
        }

        $want = new \App\Want();
        $want->fill($data);
        $want->user_id = auth()->id();
        $want->status = 'active';
        $want->expires_at = now()->addDays(7);
        $want->save();

        if ($request->expectsJson()) {
            return response()->json(['code' => 200, 'message' => '发布成功', 'data' => ['id' => $want->id]]);
        }

        return redirect('/wants')->with('success', '求购发布成功');
    }

    public function sell()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $categories = Category::orderBy('sort')->get();
        $prefill = ['title' => request('title', ''), 'author' => request('author', ''), 'publisher' => request('publisher', ''), 'category_id' => request('category_id', '')];

        return view('web.sell', compact('categories', 'prefill'));
    }

    public function deleteBook($id)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $book = Book::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

        if ($book->status !== 'removed') {
            abort(403, '只能删除已驳回的书籍');
        }

        $book->delete();

        if (request()->expectsJson()) {
            return response()->json(['code' => 200, 'message' => '已删除']);
        }
        return redirect('/my-sells')->with('success', '已删除');
    }

    public function postSell(Request $request, BookService $service)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'author' => 'required|string|max:100',
            'publisher' => 'required|string|max:100',
            'isbn' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:like_new,excellent,good,fair',
            'original_price' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'images' => 'required|array|min:2|max:5',
            'images.*' => 'file|mimetypes:image/jpeg,image/png,image/webp|max:5120',
        ]);

        $images = $request->file('images', []);
        $book = $service->submit($data, $images);

        if ($request->expectsJson()) {
            return response()->json(['code' => 200, 'message' => '提交成功，等待审核', 'data' => ['id' => $book->id]]);
        }
        return redirect('/')->with('success', '提交成功，等待审核');
    }
}
