@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">


    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết đơn hàng #{{ $order->id }}</h1>
        
        <!-- Bọc 2 nút vào chung một div để hiển thị ngang hàng -->
        <div>
            <!-- Nút Cập nhật đơn hàng -->
            <a href="{{route('admin.orders.edit', $order->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Cập nhật trạng thái đơn
            </a>
            
            <!-- Nút Quay lại -->
            <a href="{{ route('admin.orders') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin giao hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%">Tài khoản đặt hàng:</th>
                                <td>{{ $order->user->name ?? 'Không có tài khoản' }}</td>
                            </tr>
                            <tr>
                                <th>Người nhận:</th>
                                <td><strong>{{ $order->shipping_name ?? $order->user->name ?? 'Null' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><strong>{{ $order->billing_email ?? $order->user->email ?? 'Null' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td>{{ $order->shipping_phone ?? 'Null' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ giao hàng:</th>
                                <td>{{ $order->shipping_address ?? 'Null' }}</td>
                            </tr>
                            <tr>
                                <th>Ghi chú của khách:</th>
                                <td>
                                    @if($order->notes)
                                        <span class="text-danger">{{ $order->notes }}</span>
                                    @else
                                        <span class="text-muted">Không có ghi chú</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%">Trạng thái:</th>
                                <td>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Chờ xử lý</span>
                                            @break
                                        @case('confirmed')
                                            <span class="badge badge-info">Đã xác nhận</span>
                                            @break
                                        @case('shipping')
                                            <span class="badge badge-primary">Đang giao</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-success">Hoàn thành</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">Đã hủy</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">Không rõ</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <th>Phương thức thanh toán:</th>
                                <td>
                                    <span class="badge badge-dark">{{ $order->payment_method ?? 'COD' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày đặt hàng:</th>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Tổng tiền hóa đơn:</th>
                                <td>
                                    <h5 class="text-danger font-weight-bold mb-0">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ
                                    </h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Sản phẩm trong đơn</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>STT</th>
                        <th>Sản phẩm</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderDetails as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($detail->productVariant && $detail->productVariant->product)
                                    <strong>{{ $detail->productVariant->product->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Phiên bản: {{ $detail->productVariant->edition }}
                                        @if($detail->productVariant->sku)
                                            — SKU: {{ $detail->productVariant->sku }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">
                                        Sản phẩm đã bị xóa (Mã biến thể: #{{ $detail->product_variant_id }})
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($detail->price, 0, ',', '.') }} đ</td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-right font-weight-bold text-danger">
                                {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }} đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Không tìm thấy chi tiết sản phẩm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

@endsection