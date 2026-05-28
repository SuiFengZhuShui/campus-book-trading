<?php

namespace App\Services;

use App\Book;
use App\BookImage;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function submit(array $data, array $images): Book
    {
        if (count($images) < 2) {
            throw new BusinessException('请至少上传 2 张图片（封面+内页/背面）');
        }

        return DB::transaction(function () use ($data, $images) {
            $book = new Book();
            $book->fill($data);
            $book->seller_id = auth()->id();
            $book->status = 'pending_review';
            $book->submitted_at = now();
            $book->save();

            foreach ($images as $i => $file) {
                $path = $file->store("books/{$book->id}/original", 'public');
                BookImage::create([
                    'book_id' => $book->id,
                    'path' => $path,
                    'type' => $data['image_types'][$i] ?? 'other',
                    'sort' => $i,
                ]);
            }

            return $book;
        });
    }

    public function approve(int $bookId, array $data): void
    {
        $book = Book::findOrFail($bookId);

        if ($book->status !== 'pending_review') {
            throw new BusinessException('当前状态不允许此操作');
        }

        $book->fill($data);
        $book->status = 'active';
        $book->approved_at = now();
        $book->received_at = now();
        $book->save();
    }

    public function reject(int $bookId, string $reason): void
    {
        $book = Book::findOrFail($bookId);

        if ($book->status !== 'pending_review') {
            throw new BusinessException('当前状态不允许此操作');
        }

        $book->status = 'removed';
        $book->reject_reason = $reason;
        $book->save();
    }

    public function receive(int $bookId, ?float $price, ?float $costPrice): void
    {
        $book = Book::findOrFail($bookId);

        if ($book->status !== 'approved') {
            throw new BusinessException('当前状态不允许此操作');
        }

        if ($price !== null) {
            $book->price = $price;
        }
        if ($costPrice !== null) {
            $book->cost_price = $costPrice;
        }
        $book->status = 'active';
        $book->received_at = now();
        $book->save();
    }

    public function update(int $bookId, array $data): void
    {
        $book = Book::findOrFail($bookId);
        $book->fill($data)->save();
    }

    public function remove(int $bookId): void
    {
        $book = Book::findOrFail($bookId);
        $book->status = 'removed';
        $book->save();
    }

    public function suggestPrice(string $title, string $author, string $publisher, float $originalPrice): float
    {
        $avg = Book::where('title', $title)
            ->where('author', $author)
            ->where('publisher', $publisher)
            ->where('status', 'active')
            ->avg('price');

        if ($avg) {
            return round($avg, 2);
        }

        return round($originalPrice * 0.5, 2);
    }
}
