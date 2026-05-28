<?php

namespace App\Services;

use App\Want;
use App\WantFulfillment;
use App\Exceptions\BusinessException;

class WantService
{
    public function create(array $data): Want
    {
        $want = new Want();
        $want->fill($data);
        $want->user_id = auth()->id();
        $want->status = 'active';
        $want->expires_at = now()->addDays(7);
        $want->save();

        return $want;
    }

    public function fulfill(int $wantId, int $userId): void
    {
        $want = Want::findOrFail($wantId);

        if ($want->status !== 'active') {
            throw new BusinessException('该求购已结束');
        }

        if ($want->user_id === $userId) {
            throw new BusinessException('不能接自己的求购');
        }

        $exists = WantFulfillment::where('want_id', $wantId)
            ->where('fulfiller_id', $userId)
            ->exists();

        if ($exists) {
            throw new BusinessException('您已接过此求购');
        }

        WantFulfillment::create([
            'want_id' => $wantId,
            'fulfiller_id' => $userId,
            'status' => 'pending',
        ]);
    }
}
