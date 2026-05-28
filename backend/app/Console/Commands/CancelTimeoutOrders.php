<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use App\Want;
use Illuminate\Console\Command;

class CancelTimeoutOrders extends Command
{
    protected $signature = 'orders:cancel-timeout';
    protected $description = '取消超时订单并处理过期求购';

    public function handle(OrderService $service)
    {
        $service->cancelTimeoutOrders();

        // 求购过期
        $expired = Want::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info('Done.');
    }
}
