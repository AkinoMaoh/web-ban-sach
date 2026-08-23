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

            <!-- Bắt đầu khối thông tin tối ưu -->
            <div class="row text-left mb-4 p-3 bg-light rounded border">
                <!-- Cột Thông tin nhận hàng (Tỷ lệ 7) -->
                <div class="col-md-7 mb-3 mb-md-0" style="border-right: 1px solid #dee2e6;">
                    <h6 class="font-weight-bold text-secondary mb-2 text-uppercase" style="font-size: 0.85rem;">
                        <i class="fas fa-map-marker-alt mr-2"></i>Thông tin nhận hàng
                    </h6>
                    <p class="mb-1">
                        <strong>{{ $order->shipping_name }}</strong> 
                        <span class="text-muted mx-1">|</span> 
                        {{ $order->shipping_phone }}
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.4;">
                        {{ $order->shipping_address }}
                    </p>
                </div>
                
                <!-- Cột Hình thức thanh toán (Tỷ lệ 5) -->
                <div class="col-md-5 pl-md-4">
                    <h6 class="font-weight-bold text-secondary mb-2 text-uppercase" style="font-size: 0.85rem;">
                        <i class="fas fa-credit-card mr-2"></i>Hình thức thanh toán
                    </h6>
                    <p class="mb-2" style="font-size: 0.95rem;">
                        {{ $order->payment_method === 'vnpay' ? 'Thanh toán trực tuyến (VNPAY)' : 'Thanh toán khi nhận hàng (COD)' }}
                    </p>
                    <div class="pt-2 mt-2" style="border-top: 1px dashed #ced4da;">
                        <span class="text-muted">Tổng cộng:</span> 
                        <strong class="text-danger ml-1" style="font-size: 1.15rem;">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                    </div>
                </div>
            </div>
            <!-- Kết thúc khối thông tin tối ưu -->

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
