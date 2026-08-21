<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng {{ $order->order_number }}</title>
</head>
<body style="background:#f5f5f5;font-family:Arial,sans-serif;color:#252525;padding:24px;">
    <div style="background:#fff;max-width:640px;margin:0 auto;padding:32px;border-radius:10px;">
        <h2 style="margin-top:0;">Cảm ơn bạn đã đặt hàng!</h2>
        <p>Đơn <strong>{{ $order->order_number }}</strong> đã được hệ thống ghi nhận.</p>

        <table style="width:100%;border-collapse:collapse;margin:24px 0;">
            @foreach($order->orderDetails as $detail)
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;">
                        {{ $detail->product_name }} · {{ $detail->variant_name }} × {{ $detail->quantity }}
                    </td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;text-align:right;">
                        {{ number_format($detail->subtotal, 0, ',', '.') }}đ
                    </td>
                </tr>
            @endforeach
        </table>

        <p>Giao đến: <strong>{{ $order->shipping_name }}</strong>, {{ $order->shipping_address }}</p>
        <p>Tổng thanh toán: <strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></p>
        <p style="margin-top:28px;">
            <a href="{{ route('order.track', [$order->order_number, $order->tracking_token]) }}" style="background:#d35400;color:#fff;text-decoration:none;padding:12px 20px;border-radius:22px;">
                Theo dõi đơn hàng
            </a>
        </p>
    </div>
</body>
</html>
