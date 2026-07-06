@extends('layout.user')
@section('content')

<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Lịch sử mua hàng</li>
            </ol>
        </nav>
    </div>
</div>

<section class="order-history-section mb-5 pb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="serif-font font-weight-bold mb-0" style="color: #2C3E50;">Đơn hàng của bạn</h2>
            <a href="{{ route('user.shop') }}" class="btn btn-outline-dark btn-sm rounded-pill font-weight-bold px-3">
                <i class="fas fa-shopping-cart mr-1"></i> Mua thêm sách
            </a>
        </div>

        @if($orders->isEmpty())
            <!-- Giao diện khi chưa có đơn hàng nào -->
            <div class="text-center py-5 bg-white shadow-sm rounded border">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-4x text-muted" style="opacity: 0.5;"></i>
                </div>
                <h5 class="serif-font text-muted mb-3">Bạn chưa có đơn hàng nào.</h5>
                <a href="{{ route('user.shop') }}" class="btn btn-orange rounded-pill px-4">Khám phá sách ngay</a>
            </div>
        @else
            <!-- Bảng danh sách đơn hàng -->
            <div class="bg-white rounded shadow-sm border overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0 align-middle text-center" style="min-width: 800px;">
                        <thead class="bg-light border-bottom text-muted" style="font-size: 14px; text-transform: uppercase;">
                            <tr>
                                <th class="py-3">Mã đơn</th>
                                <th class="py-3">Ngày đặt hàng</th>
                                <th class="py-3">Sản phẩm</th>
                                <th class="py-3">Tổng tiền</th>
                                <th class="py-3">Trạng thái</th>
                                <th class="py-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr class="border-bottom">
                                    <!-- Mã đơn -->
                                    <td class="align-middle font-weight-bold text-dark">#{{ $order->id }}</td>
                                    
                                    <!-- Ngày đặt -->
                                    <td class="align-middle text-muted">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    
                                    <!-- Tóm tắt sản phẩm -->
                                    <td class="align-middle text-left">
                                        @if(isset($order->chi_tiet) && count($order->chi_tiet) > 0)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('uploads/products/' . $order->chi_tiet[0]->product_image) }}" class="rounded shadow-sm mr-2" style="width: 40px; height: 60px; object-fit: cover;" alt="Book">
                                                <div>
                                                    <span class="d-block text-dark font-weight-bold" style="font-size: 14px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                                        {{ $order->chi_tiet[0]->product_name }}
                                                    </span>
                                                    @if(count($order->chi_tiet) > 1)
                                                        <small class="text-muted">và {{ count($order->chi_tiet) - 1 }} sản phẩm khác...</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Không có dữ liệu</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Tổng tiền -->
                                    <td class="align-middle font-weight-bold" style="color: var(--primary-color); font-size: 15px;">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                    </td>
                                    
                                    <!-- Trạng thái -->
                                    <td class="align-middle">
                                        @if($order->status == 'pending')
                                            <span class="badge badge-warning px-3 py-2 text-dark shadow-sm" style="border-radius: 6px;">Chờ xác nhận</span>
                                        @elseif($order->status == 'shipping')
                                            <span class="badge badge-info px-3 py-2 text-white shadow-sm" style="border-radius: 6px;">Đang giao</span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge badge-success px-3 py-2 shadow-sm" style="border-radius: 6px;">Thành công</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge badge-danger px-3 py-2 shadow-sm" style="border-radius: 6px;">Đã hủy</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Thao tác -->
                                    <td class="align-middle">
                                        <a href="{{ route('user.orderHistory.show', $order->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 font-weight-bold">
                                            Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
           
            <!-- Phân trang -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</section>
@endsection 
