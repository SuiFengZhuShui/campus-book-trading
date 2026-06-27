<?php

namespace App\Services;

use App\Book;
use App\BookImage;
use App\WantFulfillment;
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
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $path = $file->store("books/{$book->id}/original", 'public');
                } else {
                    // 路径字符串：从 temp/ 目录移动到书籍目录
                    $tempPath = str_replace('/storage/', '', (string)$file);
                    $filename = basename($tempPath);
                    $destPath = "books/{$book->id}/original/{$filename}";
                    \Storage::disk('public')->move($tempPath, $destPath);
                    $path = $destPath;
                }
                BookImage::create([
                    'book_id' => $book->id,
                    'path' => $path,
                    'type' => $data['image_types'][$i] ?? 'other',
                    'sort' => $i,
                ]);
            }

            // 关联求购接单：提交书即创建/更新接单记录并关闭求购
            if (!empty($data['want_id'])) {
                WantFulfillment::updateOrCreate(
                    ['want_id' => $data['want_id'], 'fulfiller_id' => auth()->id()],
                    ['book_id' => $book->id, 'status' => 'listed']
                );
                \App\Want::where('id', $data['want_id'])->update(['status' => 'closed']);
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

        // 关联求购：审批通过后将接单标记为完成
        $fulfillment = WantFulfillment::where('book_id', $bookId)->where('status', 'listed')->first();
        if ($fulfillment) {
            $fulfillment->update(['status' => 'completed']);
        }
    }

    public function reject(int $bookId, string $reason): void
    {
        $book = Book::findOrFail($bookId);

        if ($book->status !== 'pending_review') {
            throw new BusinessException('当前状态不允许此操作');
        }

        $book->status = 'rejected';
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
