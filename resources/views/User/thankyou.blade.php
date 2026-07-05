@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Đặt hàng thành công</li>
            </ol>
        </nav>
    </div>
</div>

<section class="thank-you-section py-5 mb-5">
    <div class="container text-center">
        <div class="bg-white p-5 rounded shadow-sm border mx-auto" style="max-width: 650px;">
            
            <!-- Icon Success (Tích xanh) -->
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style="width: 90px; height: 90px; font-size: 40px;">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            <!-- Lời cảm ơn -->
            <h2 class="serif-font font-weight-bold mb-3" style="color: var(--text-main);">ĐẶT HÀNG THÀNH CÔNG!</h2>
            
            <p class="text-muted mb-4" style="font-size: 16px;">
                {{ $message ?? 'Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi.' }}
            </p>

            <!-- Box Mã Đơn Hàng -->
            <div class="bg-light p-3 rounded border mb-4 mx-auto" style="max-width: 400px; border-color: #EEEEEE !important;">
                <span class="text-muted" style="font-size: 15px;">Mã đơn hàng của bạn là:</span><br>
                <strong class="d-block mt-1" style="font-size: 28px; color: var(--primary-color);">#{{ $orderId ?? 'N/A' }}</strong>
            </div>

            <p class="text-muted mb-5" style="font-size: 15px; line-height: 1.6;">
                Chúng tôi sẽ sớm liên hệ với bạn theo số điện thoại đã cung cấp để xác nhận đơn hàng và tiến hành giao hàng.
            </p>

            <!-- Nút quay lại mua sắm -->
            <a href="{{ route('user.index') }}" class="btn btn-orange rounded-pill px-5 py-3 font-weight-bold shadow-sm text-uppercase" style="font-size: 15px; letter-spacing: 0.5px;">
                <i class="fas fa-shopping-bag mr-2"></i> Tiếp tục mua sắm
            </a>
        </div>
    </div>
</section>
@endsection