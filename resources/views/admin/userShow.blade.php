@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="fas fa-user-circle mr-2"></i>
            Chi tiết người dùng
        </h3>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">

            <div class="text-center mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=120"
                     class="rounded-circle border shadow">

                <h4 class="mt-3">{{ $user->name }}</h4>

                @if($user->role == 1)
                    <span class="badge badge-danger">Admin</span>
                @else
                    <span class="badge badge-success">Khách hàng</span>
                @endif
            </div>

          <table class="table table-bordered">
    <tr>
        <th width="25%">ID</th>
        <td>{{ $user->id }}</td>
    </tr>

    <tr>
        <th>Họ và tên</th>
        <td>{{ $user->name }}</td>
    </tr>

    <tr>
        <th>Email</th>
        <td>{{ $user->email }}</td>
    </tr>

    <tr>
        <th>Số điện thoại</th>
        <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
    </tr>

    <tr>
        <th>Địa chỉ</th>
        <td>
            {{-- Lấy địa chỉ từ bảng user_addresses nếu có, ngược lại fallback về cột address cũ --}}
            {{ $user->defaultAddress->specific_address ?? $user->address ?? 'Chưa cập nhật' }}
        </td>
    </tr>

    <tr>
        <th>Giới tính</th>
        <td>
            {{-- So sánh trực tiếp chuỗi 'male' / 'female' --}}
            @if($user->gender === 'male')
                Nam
            @elseif($user->gender === 'female')
                Nữ
            @else
                Chưa cập nhật
            @endif
        </td>
    </tr>

    <tr>
        <th>Vai trò</th>
        <td>
            {{ $user->role == 1 ? 'Admin' : 'Khách hàng' }}
        </td>
    </tr>

    <tr>
        <th>Ngày tạo</th>
        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'Chưa cập nhật' }}</td>
    </tr>

    <tr>
        <th>Cập nhật lần cuối</th>
        <td>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'Chưa cập nhật' }}</td>
    </tr>
</table>

        </div>
    </div>
    
    <!-- Lịch sử mua hàng -->
    <div class="card shadow mt-4">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="fas fa-shopping-cart mr-2"></i>
                Lịch sử mua hàng
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>

                                <td>
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                </td>

                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge badge-warning">Chờ xử lý</span>
                                    @elseif($order->status == 'confirmed')
                                        <span class="badge badge-info">Đã xác nhận</span>
                                    @elseif($order->status == 'shipping')
                                        <span class="badge badge-primary">Đang giao</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge badge-success">Hoàn thành</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge badge-danger">Đã hủy</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $order->status }}</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-info text-white"
                                            data-toggle="collapse"
                                            data-target="#order{{ $order->id }}"
                                            title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>

                            </tr>

                            <!-- Chi tiết đơn hàng -->
                            <tr class="collapse" id="order{{ $order->id }}">
                                <td colspan="5" class="p-0">
                                    <div class="p-4 bg-light">

                                        {{-- Tiêu đề --}}
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="mr-2">
                                                <i class="fas fa-box-open text-primary"></i>
                                            </div>

                                            <div>
                                                <h5 class="font-weight-bold text-dark mb-0">
                                                    Chi tiết đơn hàng
                                                </h5>

                                                <small class="text-muted">
                                                    {{ $order->order_number }}
                                                </small>
                                            </div>
                                        </div>


                                        {{-- Danh sách sản phẩm --}}
                                        <div class="bg-white border rounded">

                                            @forelse($order->orderDetails as $detail)

                                                <div class="p-3 border-bottom">
                                                    <div class="row align-items-center">

                                                        {{-- Thông tin sản phẩm --}}
                                                        <div class="col-md-7">
                                                            <div class="font-weight-bold text-dark">
                                                                {{ $detail->product_name }}
                                                            </div>

                                                            @if($detail->variant_name)
                                                                <div class="small text-muted mt-1">
                                                                    Phân loại:
                                                                    {{ $detail->variant_name }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Số lượng --}}
                                                        <div class="col-md-2 text-md-center mt-2 mt-md-0">
                                                            <span class="small text-muted">
                                                                Số lượng
                                                            </span>

                                                            <div class="font-weight-bold">
                                                                {{ $detail->quantity }}
                                                            </div>
                                                        </div>

                                                        {{-- Thành tiền --}}
                                                        <div class="col-md-3 text-md-right mt-2 mt-md-0">
                                                            <span class="small text-muted">
                                                                Thành tiền
                                                            </span>

                                                            <div class="font-weight-bold text-dark">
                                                                {{ number_format($detail->subtotal, 0, ',', '.') }} đ
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            @empty

                                                <div class="text-center text-muted py-4">
                                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                                    <div>Đơn hàng này chưa có sản phẩm.</div>
                                                </div>

                                            @endforelse

                                        </div>


                                        {{-- Tổng tiền --}}
                                        <div class="row justify-content-end mt-4">
                                            <div class="col-md-5">

                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Tạm tính</span>
                                                    <span>
                                                        {{ number_format($order->subtotal_amount, 0, ',', '.') }} đ
                                                    </span>
                                                </div>

                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Giảm giá</span>

                                                    <span class="text-danger">
                                                        -{{ number_format($order->discount_amount, 0, ',', '.') }} đ
                                                    </span>
                                                </div>

                                                <div class="d-flex justify-content-between mb-3">
                                                    <span class="text-muted">Phí vận chuyển</span>

                                                    <span>
                                                        {{ number_format($order->shipping_fee, 0, ',', '.') }} đ
                                                    </span>
                                                </div>

                                                <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                                    <strong class="text-dark">
                                                        Tổng tiền
                                                    </strong>

                                                    <strong class="text-primary" style="font-size: 1.15rem;">
                                                        {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                                    </strong>
                                                </div>

                                            </div>
                                        </div>


                                        {{-- Thanh toán + Giao hàng --}}
                                        <div class="row mt-4">

                                            {{-- Thanh toán --}}
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <div class="bg-white border rounded p-3 h-100">

                                                    <h6 class="font-weight-bold text-dark mb-3">
                                                        <i class="fas fa-credit-card text-primary mr-2"></i>
                                                        Thông tin thanh toán
                                                    </h6>

                                                    <div class="mb-2">
                                                        <span class="text-muted">
                                                            Phương thức:
                                                        </span>

                                                        @if($order->payment_method == 'cod')
                                                            <strong>COD</strong>
                                                        @else
                                                            <strong>
                                                                {{ $order->payment_method ?? 'Chưa cập nhật' }}
                                                            </strong>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <span class="text-muted">
                                                            Trạng thái:
                                                        </span>

                                                        @if($order->payment_status == 'paid')
                                                            <span class="badge badge-success">
                                                                Đã thanh toán
                                                            </span>
                                                        @elseif($order->payment_status == 'pending')
                                                            <span class="badge badge-warning">
                                                                Chờ thanh toán
                                                            </span>
                                                        @elseif($order->payment_status == 'unpaid')
                                                            <span class="badge badge-secondary">
                                                                Chưa thanh toán
                                                            </span>
                                                        @elseif($order->payment_status == 'failed')
                                                            <span class="badge badge-danger">
                                                                Thanh toán thất bại
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">
                                                                {{ $order->payment_status ?? 'Chưa cập nhật' }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>


                                            {{-- Giao hàng --}}
                                            <div class="col-md-6">
                                                <div class="bg-white border rounded p-3 h-100">

                                                    <h6 class="font-weight-bold text-dark mb-3">
                                                        <i class="fas fa-truck text-primary mr-2"></i>
                                                        Thông tin giao hàng
                                                    </h6>

                                                    <div class="mb-2">
                                                        <span class="text-muted">
                                                            Người nhận:
                                                        </span>

                                                        <strong>
                                                            {{ $order->shipping_name ?? 'Chưa cập nhật' }}
                                                        </strong>
                                                    </div>

                                                    <div class="mb-2">
                                                        <span class="text-muted">
                                                            Số điện thoại:
                                                        </span>

                                                        {{ $order->shipping_phone ?? 'Chưa cập nhật' }}
                                                    </div>

                                                    <div>
                                                        <span class="text-muted">
                                                            Địa chỉ:
                                                        </span>

                                                        {{ $order->shipping_address ?? 'Chưa cập nhật' }}
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Người dùng chưa có đơn hàng nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

@endsection