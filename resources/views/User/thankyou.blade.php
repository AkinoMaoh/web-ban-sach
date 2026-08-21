@extends('layout.user')

@section('content')
<section class="py-5 bg-light">
    <div class="container" style="max-width: 780px;">
        <div class="bg-white p-4 p-md-5 rounded shadow-sm border text-center">
            <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle mb-4" style="width: 82px; height: 82px; font-size: 36px;">
                <i class="fas fa-check"></i>
            </div>

            <h2 class="serif-font font-weight-bold mb-3">Cảm ơn bạn đã đặt hàng!</h2>
            <p class="text-muted mb-4">{{ $message ?? 'Đơn hàng đã được ghi nhận thành công.' }}</p>

            <div class="bg-light rounded border p-3 mb-4">
                <small class="text-muted text-uppercase font-weight-bold">Mã đơn hàng</small>
                <strong class="d-block mt-1" style="font-size: 26px; color: var(--primary-color);">{{ $order->order_number }}</strong>
            </div>

            <div class="row text-left mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="font-weight-bold">Giao đến</h6>
                    <p class="mb-1"><strong>{{ $order->shipping_name }}</strong> · {{ $order->shipping_phone }}</p>
                    <p class="text-muted mb-0">{{ $order->shipping_address }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-bold">Thanh toán</h6>
                    <p class="mb-1">{{ $order->payment_method === 'vnpay' ? 'VNPAY' : 'Thanh toán khi nhận hàng' }}</p>
                    <p class="mb-0">Tổng cộng: <strong class="text-danger">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></p>
                </div>
            </div>

            @if($order->discount_amount > 0)
                <p class="alert alert-success py-2">
                    Voucher {{ $order->voucher_code }} đã giúp bạn tiết kiệm {{ number_format($order->discount_amount, 0, ',', '.') }}đ.
                </p>
            @endif

            <div class="d-flex flex-column flex-sm-row justify-content-center mt-4">
                @auth
                    <a href="{{ route('user.orderHistory.show', $order->id) }}" class="btn btn-outline-dark rounded-pill px-4 py-2 mb-2 mb-sm-0 mr-sm-2">
                        Xem chi tiết đơn
                    </a>
                @else
                    <a href="{{ route('order.track', [$order->order_number, $order->tracking_token]) }}" class="btn btn-outline-dark rounded-pill px-4 py-2 mb-2 mb-sm-0 mr-sm-2">
                        Theo dõi đơn hàng
                    </a>
                @endauth
                <a href="{{ route('user.index') }}" class="btn btn-orange rounded-pill px-4 py-2">Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
</section>
@endsection

@if($clearCheckoutDraft ?? false)
    @push('scripts')
        <script>
            try {
                sessionStorage.removeItem('web-ban-sach:checkout-draft:v2');
            } catch (error) {
                // Storage có thể bị trình duyệt chặn.
            }
        </script>
    @endpush
@endif
