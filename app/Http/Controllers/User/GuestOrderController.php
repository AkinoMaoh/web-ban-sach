<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class GuestOrderController extends Controller
{
    public function show(string $orderNumber, string $token): View
    {
        $order = Order::query()
            ->with('orderDetails')
            ->where('order_number', $orderNumber)
            ->where('tracking_token', $token)
            ->firstOrFail();

        return view('User.order_tracking', compact('order'));
    }
}
