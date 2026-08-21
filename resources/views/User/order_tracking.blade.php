@extends('layout.user')

@section('content')
@php
    $statusLabels = [
        'pending' => ['Chờ xác nhận', 'warning'],
        'confirmed' => ['Đã xác nhận', 'primary'],
        'shipping' => ['Đang giao hàng', 'info'],
        'completed' => ['Đã hoàn thành', 'success'],
        'cancelled' => ['Đã hủy', 'danger'],
    ];
    [$statusText, $statusColor] = $statusLabels[$order->status] ?? [$order->status, 'secondary'];
@endphp

<section class="py-5 bg-light">
    <div class="container" style="max-width: 980px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted small font-weight-bold mb-1">Tra cứu đơn hàng</p>
                <h2 class="font-weight-bold mb-0">{{ $order->order_number }}</h2>
            </div>
            <span class="badge badge-{{ $statusColor }} px-3 py-2 mt-2 mt-sm-0">{{ $statusText }}</span>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">Sản phẩm</div>
                    <div class="card-body p-0">
                        @foreach($order->orderDetails as $detail)
                            <div class="d-flex justify-content-between border-bottom p-3">
                                <div>
                                    <strong>{{ $detail->product_name }}</strong>
                                    <div class="small text-muted">{{ $detail->variant_name }} · x{{ $detail->quantity }}</div>
                                </div>
                                <strong>{{ number_format($detail->subtotal, 0, ',', '.') }}đ</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white font-weight-bold">Giao hàng</div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->shipping_name }}</strong></p>
                        <p class="mb-1">{{ $order->shipping_phone }}</p>
                        <p class="mb-0 text-muted">{{ $order->shipping_address }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">Thanh toán</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->subtotal_amount, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Vận chuyển</span>
                            <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 font-weight-bold">
                            <span>Tổng cộng</span>
                            <span class="text-danger">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
