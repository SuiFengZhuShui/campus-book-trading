<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\OrderItem;
use App\Services\BookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('images')
            ->where('status', 'active')
            ->when($request->category_id, function ($q, $v) {
                $q->where('category_id', $v);
            })
            ->when($request->condition, function ($q, $v) {
                $q->where('condition', $v);
            })
            ->when($request->course_id, function ($q, $v) {
                $q->where('course_id', $v);
            })
            ->when($request->keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('title', 'like', "%{$v}%")
                      ->orWhere('author', 'like', "%{$v}%");
                });
            })
            ->when($request->sort, function ($q, $v) {
                if ($v === 'price_asc') $q->orderBy('price', 'asc');
                elseif ($v === 'price_desc') $q->orderBy('price', 'desc');
                elseif ($v === 'condition') $q->orderByRaw("FIELD(books.condition, 'like_new','excellent','good','fair')");
                else $q->orderBy('received_at', 'desc');
            }, function ($q) {
                $q->orderBy('received_at', 'desc');
            })
            ->paginate($request->per_page ?? 15);

        $list = $books->map(function ($book) {
            $cover = $book->images->where('type', 'cover')->first();
            return [
                'id' => $book->id,
                'seller_id' => $book->seller_id,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'cover_img' => $cover ? '/storage/' .$cover->path : null,
                'condition' => $book->condition,
                'condition_label' => $this->conditionLabel($book->condition),
                'price' => $book->price,
                'original_price' => $book->original_price,
                'received_at' => $book->received_at ? $book->received_at->toDateTimeString() : null,
            ];
        });

        return $this->paginate($list, [
            'current_page' => $books->currentPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
            'last_page' => $books->lastPage(),
        ]);
    }

    public function show($id)
    {
        $book = Book::with(['images', 'reviews.user', 'category', 'course.major.college'])->findOrFail($id);

        $data = [
            'id' => $book->id,
            'seller_id' => $book->seller_id,
            'title' => $book->title,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'isbn' => $book->isbn,
            'category' => $book->category ? $book->category->name : null,
            'course' => $book->course ? $book->course->name : null,
            'status' => $book->status,
            'condition' => $book->condition,
            'condition_label' => $this->conditionLabel($book->condition),
            'original_price' => $book->original_price,
            'price' => $book->price,
            'description' => $book->description,
            'images' => $book->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => '/storage/' .$img->path,
                    'type' => $img->type,
                ];
            }),
            'reviews' => $book->reviews->map(function ($r) {
                return [
                    'id' => $r->id,
                    'book_rating' => $r->book_rating,
                    'service_rating' => $r->service_rating,
                    'comment' => $r->comment,
                    'user_name' => $r->user ? $r->user->name : '匿名',
                    'created_at' => $r->created_at->toDateTimeString(),
                ];
            }),
            'submitted_at' => $book->submitted_at ? $book->submitted_at->toDateTimeString() : null,
        ];

        if ($this->canViewSeller($book)) {
            $data['seller'] = [
                'id' => $book->seller->id,
                'name' => $book->seller->name,
                'phone' => $this->maskPhone($book->seller->phone),
            ];
            // 卖家历史评价
            $sellerReviews = \App\Review::whereIn('book_id', function ($q) use ($book) {
                $q->select('id')->from('books')->where('seller_id', $book->seller_id);
            })->with('user')->orderBy('created_at', 'desc')->get();
            $data['seller_reviews'] = $sellerReviews->map(function ($r) {
                return [
                    'id' => $r->id,
                    'book_rating' => $r->book_rating,
                    'service_rating' => $r->service_rating,
                    'comment' => $r->comment,
                    'user_name' => $r->user ? $r->user->name : '匿名',
                    'created_at' => $r->created_at->toDateTimeString(),
                ];
            });
        }

        return $this->success($data);
    }

    public function submit(Request $request, BookService $service)
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
            'description' => 'nullable|string|max:500',
            'images' => 'required_without:image_urls|array|min:2|max:5',
            'images.*' => 'file|mimetypes:image/jpeg,image/png,image/webp|max:5120',
            'image_urls' => 'required_without:images|array|min:2|max:5',
            'image_urls.*' => 'string',
            'image_types' => 'nullable|array',
            'want_id' => 'nullable|integer',
        ]);

        $imageUrls = $request->input('image_urls', []);
        if (!empty($imageUrls)) {
            $data['images'] = $imageUrls;
        }

        $images = !empty($imageUrls) ? $imageUrls : $request->file('images', []);
        $book = $service->submit($data, $images);

        return $this->success(['id' => $book->id], '提交成功，等待审核');
    }

    public function myBooks(Request $request)
    {
        $books = Book::with(['images', 'orderItems.order'])
            ->where('seller_id', auth()->id())
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $list = $books->map(function ($book) {
            $cover = $book->images->where('type', 'cover')->first();
            $orderItem = $book->orderItems->first();
            return [
                'id' => $book->id,
                'title' => $book->title,
                'cover_img' => $cover ? '/storage/' .$cover->path : null,
                'status' => $book->status,
                'status_label' => $this->statusLabel($book->status),
                'price' => $book->price,
                'cost_price' => $book->cost_price,
                'reject_reason' => $book->reject_reason,
                'seller_paid' => $book->seller_paid,
                'order_id' => $orderItem ? $orderItem->order_id : null,
                'submitted_at' => $book->submitted_at ? $book->submitted_at->toDateTimeString() : null,
            ];
        });

        return $this->paginate($list, [
            'current_page' => $books->currentPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
            'last_page' => $books->lastPage(),
        ]);
    }

    public function destroy($id)
    {
        $book = Book::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

        if (!in_array($book->status, ['removed', 'rejected'])) {
            return $this->error('只能删除已驳回或已下架的书籍', 403);
        }

        $book->delete();
        return $this->success(null, '已删除');
    }

    private function canViewSeller(Book $book): bool
    {
        $user = request()->user();
        if (!$user) return false;

        return OrderItem::where('book_id', $book->id)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->whereIn('status', ['paid', 'confirmed', 'picked_up']);
            })->exists();
    }

    private function maskPhone(string $phone): string
    {
        return substr($phone, 0, 3) . '****' . substr($phone, 7);
    }

    private function conditionLabel(string $condition): string
    {
        $map = [
            'like_new' => '全新',
            'excellent' => '几乎全新',
            'good' => '正常使用',
            'fair' => '较旧',
        ];
        return $map[$condition] ?? $condition;
    }

    private function statusLabel(string $status): string
    {
        $map = [
            'pending_review' => '待审核',
            'approved' => '已通过',
            'active' => '在售',
            'sold' => '已售出',
            'removed' => '已下架',
            'rejected' => '已驳回',
        ];
        return $map[$status] ?? $status;
    }
}
