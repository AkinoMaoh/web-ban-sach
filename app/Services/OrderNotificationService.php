<?php

namespace App\Services;

use App\Mail\OrderPlaced;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderNotificationService
{
    public function notifyAdmins(Order $order, string $message): void
    {
        try {
            User::query()
                ->where('role', 1)
                ->each(function (User $admin) use ($order, $message): void {
                    Notification::query()->create([
                        'user_id' => $admin->id,
                        'order_id' => $order->id,
                        'message' => $message,
                        'is_read' => false,
                        'target_url' => route('admin.orders.edit', $order->id),
                    ]);
                });
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function sendOrderConfirmation(Order $order): void
    {
        if (! $order->billing_email) {
            return;
        }

        try {
            $order->loadMissing('orderDetails');
            Mail::to($order->billing_email)->send(new OrderPlaced($order));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
