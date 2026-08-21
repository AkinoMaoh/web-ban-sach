@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Các nút hành động -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chi tiết đơn hàng <span class="text-primary">{{ $order->order_number ?? '#'.$order->id }}</span></h1>
        
        <div class="d-flex align-items-center">
            <!-- Nút Cập nhật trạng thái đơn -->
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-primary shadow-sm mr-2 font-weight-bold">
                <i class="fas fa-edit fa-sm mr-1"></i> Cập nhật trạng thái đơn
            </a>
            
            <!-- Nút Quay lại danh sách -->
            <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-secondary shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Hàng thông tin Giao hàng & Thanh toán -->
    <div class="row">
        
        <!-- CỘT TRÁI: Thông tin giao hàng -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shipping-fast mr-2"></i> Thông tin giao hàng
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted" width="38%">Tài khoản đặt:</th>
                                <td class="font-weight-bold text-dark">{{ $order->user->name ?? 'Không có tài khoản' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Người nhận:</th>
                                <td><strong class="text-dark">{{ $order->shipping_name ?? ($order->user->name ?? 'N/A') }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email liên hệ:</th>
                                <td><strong class="text-dark">{{ $order->billing_email ?? ($order->user->email ?? 'N/A') }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Số điện thoại:</th>
                                <td><span class="font-weight-bold text-dark">{{ $order->shipping_phone ?? 'N/A' }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Địa chỉ nhận hàng:</th>
                                <td class="text-dark">{{ $order->shipping_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Ghi chú của khách:</th>
                                <td>
                                    @if($order->notes)
                                        <span class="text-danger font-italic bg-light p-2 rounded d-block border">{{ $order->notes }}</span>
                                    @else
                                        <span class="text-muted font-italic">Không có ghi chú</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: Thông tin thanh toán & Tổng tiền -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> Thông tin thanh toán
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted" width="38%">Trạng thái đơn:</th>
                                <td>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge badge-warning px-2 py-1 font-weight-bold">Chờ xử lý</span>
                                            @break
                                        @case('confirmed')
                                            <span class="badge badge-info px-2 py-1 font-weight-bold">Đã xác nhận</span>
                                            @break
                                        @case('shipping')
                                            <span class="badge badge-primary px-2 py-1 font-weight-bold">Đang giao</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-success px-2 py-1 font-weight-bold">Hoàn thành</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger px-2 py-1 font-weight-bold">Đã hủy</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary px-2 py-1 font-weight-bold">Không rõ</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Thanh toán:</th>
                                <td>
                                    <span class="badge badge-dark px-2 py-1">{{ $order->payment_method ?? 'COD' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Ngày đặt hàng:</th>
                                <td class="text-dark">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tạm tính:</th>
                                <td>
                                    <span class="font-weight-bold text-dark">
                                        {{ number_format($order->subtotal_amount ?: ($order->total_amount - $order->shipping_fee + $order->discount_amount), 0, ',', '.') }} đ
                                    </span>
                                </td>
                            </tr>
                            @if($order->discount_amount > 0)
                                <tr>
                                    <th class="text-muted">Voucher {{ $order->voucher_code ? '(' . $order->voucher_code . ')' : '' }}:</th>
                                    <td class="font-weight-bold text-success">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</td>
                                </tr>
                            @endif
                            <tr>
                                <th class="text-muted">Phí vận chuyển:</th>
                                <td>
                                    <span class="font-weight-bold text-dark">
                                        {{ number_format($order->shipping_fee, 0, ',', '.') }} đ
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tổng thanh toán:</th>
                                <td>
                                    <h5 class="text-danger font-weight-bold mb-0">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                    </h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Bảng danh sách sản phẩm trong đơn -->
    <div class="card shadow mb-4 border-0 rounded-lg">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-boxes mr-2"></i> Sản phẩm trong đơn hàng
            </h6>
        </div>
        <div class="card-body px-0 pb-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.82rem;">
                        <tr>
                            <th class="py-3 pl-4" width="6%">STT</th>
                            <th class="py-3" width="44%">Sản phẩm</th>
                            <th class="py-3 text-right" width="15%">Đơn giá</th>
                            <th class="py-3 text-center" width="15%">Số lượng</th>
                            <th class="py-3 text-right pr-4" width="20%">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderDetails as $index => $detail)
                            <tr>
                                <td class="pl-4 font-weight-bold text-primary">{{ $index + 1 }}</td>
                                <td>
                                    @if($detail->productVariant && $detail->productVariant->product)
                                        <strong class="text-dark">{{ $detail->productVariant->product->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Phiên bản: <span class="font-weight-bold text-secondary">{{ $detail->productVariant->edition }}</span>
                                            @if($detail->productVariant->sku)
                                                — SKU: <span class="font-weight-bold text-secondary">{{ $detail->productVariant->sku }}</span>
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-danger font-italic">
                                            Sản phẩm đã bị xóa (Mã biến thể: #{{ $detail->product_variant_id }})
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right text-dark">{{ number_format($detail->price, 0, ',', '.') }} đ</td>
                                <td class="text-center font-weight-bold">{{ $detail->quantity }}</td>
                                <td class="text-right pr-4 font-weight-bold text-danger">
                                    {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Không tìm thấy chi tiết sản phẩm nào trong đơn hàng này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
