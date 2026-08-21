<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Response;

class GuestOrderController extends Controller
{
    public function show(string $orderNumber, string $token): Response
    {
        $order = Order::query()
            ->with('orderDetails')
            ->where('order_number', $orderNumber)
            ->where('tracking_token', $token)
            ->firstOrFail();

        return response()
            ->view('User.order_tracking', compact('order'))
            ->header('Cache-Control', 'private, no-store, max-age=0')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
