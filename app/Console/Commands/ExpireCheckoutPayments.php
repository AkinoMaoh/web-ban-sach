<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderLifecycleService;
use Illuminate\Console\Command;
use Throwable;

class ExpireCheckoutPayments extends Command
{
    protected $signature = 'checkout:expire-payments {--limit=500}';

    protected $description = 'Huy cac don VNPAY qua han va hoan voucher cung ton kho';

    public function handle(OrderLifecycleService $lifecycleService): int
    {
        $limit = max((int) $this->option('limit'), 1);
        $expired = 0;
        $failed = 0;

        Order::query()
            ->where('payment_method', 'vnpay')
            ->where('payment_status', Order::PAYMENT_PENDING)
            ->where('status', Order::STATUS_PENDING)
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', now())
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id')
            ->each(function (int $orderId) use ($lifecycleService, &$expired, &$failed): void {
                try {
                    if ($lifecycleService->expirePayment($orderId)) {
                        $expired++;
                    }
                } catch (Throwable $exception) {
                    report($exception);
                    $failed++;
                }
            });

        $this->info("Da het han {$expired} don thanh toan; loi {$failed} don.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
