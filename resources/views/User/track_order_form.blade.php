@extends('layout.user') {{-- Nhớ đổi tên layout này cho khớp với file master layout của nhóm nhé --}}

@section('content')
<div class="container py-5" style="min-height: 60vh;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-lg mt-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-search-location mb-3" style="font-size: 3rem; color: #f56a00;"></i>
                        <h4 class="font-weight-bold" style="color: #333;">Tra Cứu Đơn Hàng</h4>
                        <p class="text-muted small">Vui lòng nhập thông tin để kiểm tra trạng thái đơn hàng của bạn</p>
                    </div>
                    
                    {{-- Hiển thị thông báo lỗi nếu nhập sai mã hoặc SĐT --}}
                    @if(session('error'))
                        <div class="alert alert-danger text-center small rounded">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('order.track.process') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-secondary small">Mã đơn hàng <span class="text-danger">*</span></label>
                            <input type="text" name="order_number" class="form-control form-control-lg" placeholder="VD: DH260823..." required style="font-size: 0.95rem;">
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-secondary small">Số điện thoại đặt hàng <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_phone" class="form-control form-control-lg" placeholder="Nhập số điện thoại..." required style="font-size: 0.95rem;">
                        </div>
                        <button type="submit" class="btn btn-block text-white font-weight-bold py-2" style="background-color: #f56a00; border-radius: 8px; font-size: 1.1rem;">
                            Kiểm tra tiến độ
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('user.index') }}" class="text-muted small" style="text-decoration: none;">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection