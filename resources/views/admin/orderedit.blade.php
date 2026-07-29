@extends('admin.layout')

@section('admin_content')
<style>
    #status option:disabled {
        color: #adb5bd;
        background-color: #f1f1f1;
        font-style: italic;
    }
    .table-middle td, .table-middle th {
        vertical-align: middle !important;
    }
</style>

<div class="container-fluid">

    <!-- Tiêu đề và Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Cập nhật đơn hàng <span class="text-primary">#{{ $order->id }}</span></h1>
        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Vui lòng kiểm tra lại:</h6>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- CỘT TRÁI: Thông tin giao hàng & Danh sách sản phẩm -->
            <div class="col-lg-8">
                
                <!-- Bảng sản phẩm trong đơn -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-box-open mr-2"></i> Sản phẩm trong đơn hàng</h6>
                    </div>
                    <div class="card-body px-0 pb-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-middle mb-0" width="100%" cellspacing="0">
                                <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.82rem;">
                                    <tr>
                                        <th class="py-3 pl-3" width="5%">STT</th>
                                        <th class="py-3">Sản phẩm</th>
                                        <th class="py-3 text-right" width="18%">Đơn giá</th>
                                        <th class="py-3 text-center" width="10%">SL</th>
                                        <th class="py-3 text-right pr-3" width="20%">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->orderDetails as $index => $detail)
                                        <tr>
                                            <td class="pl-3 text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                @if($detail->productVariant && $detail->productVariant->product)
                                                    <strong class="text-dark">{{ $detail->productVariant->product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Phiên bản: {{ $detail->productVariant->edition }}
                                                        @if($detail->productVariant->sku)
                                                            — SKU: {{ $detail->productVariant->sku }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted font-italic">
                                                        Sản phẩm đã bị xóa (Mã biến thể: #{{ $detail->product_variant_id }})
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-right text-muted">{{ number_format($detail->price, 0, ',', '.') }} đ</td>
                                            <td class="text-center font-weight-bold">{{ $detail->quantity }}</td>
                                            <td class="text-right pr-3 font-weight-bold text-danger">
                                                {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }} đ
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Không tìm thấy chi tiết sản phẩm.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Thông tin khách hàng & Giao hàng -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shipping-fast mr-2"></i> Thông tin giao hàng</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted" width="30%">Tài khoản đặt:</th>
                                    <td class="font-weight-bold text-dark">{{ $order->user->name ?? 'Khách vãng lai' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Người nhận:</th>
                                    <td class="font-weight-bold text-dark">{{ $order->shipping_name ?? $order->user->name ?? 'Null' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Email liên hệ:</th>
                                    <td>{{ $order->billing_email ?? $order->user->email ?? 'Null' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Số điện thoại:</th>
                                    <td class="font-weight-bold text-dark">{{ $order->shipping_phone ?? 'Null' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Địa chỉ nhận:</th>
                                    <td class="text-dark">{{ $order->shipping_address ?? 'Null' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Ghi chú:</th>
                                    <td>
                                        @if($order->notes)
                                            <span class="text-danger font-italic">"{{ $order->notes }}"</span>
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

            <!-- CỘT PHẢI: Trạng thái đơn hàng, Thanh toán & Hành động -->
            <div class="col-lg-4">
                
                <!-- Thẻ cập nhật trạng thái (Sticky để luôn theo dõi khi cuộn) -->
                <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px; z-index: 100;">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks mr-2"></i> Xử lý đơn hàng</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Thanh toán & Tài chính -->
                        <div class="bg-light p-3 rounded-lg mb-4 border">
                            <div class="d-flex justify-content-between mb-2 text-muted small">
                                <span>Phương thức:</span>
                                <span class="badge badge-dark px-2 py-1">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted small">
                                <span>Ngày đặt:</span>
                                <span>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Tiền sản phẩm:</span>
                                <span>{{ number_format($order->total_amount - $order->shipping_fee, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Phí vận chuyển:</span>
                                <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                <span class="font-weight-bold text-dark">TỔNG CỘNG:</span>
                                <span class="font-weight-bold text-danger h5 mb-0">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                </span>
                            </div>
                        </div>

                        @php
                            $statusOrder = ['pending', 'confirmed', 'shipping', 'completed'];
                            $currentIndex = array_search($order->status, $statusOrder);
                        @endphp

                        <!-- Form chọn trạng thái -->
                        <div class="form-group mb-4">
                            <label for="status" class="font-weight-bold text-dark small text-uppercase">Trạng thái đơn hàng <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }} {{ $currentIndex !== false && $currentIndex > array_search('pending', $statusOrder) ? 'disabled' : '' }}>
                                    Chờ xử lý{{ $currentIndex !== false && $currentIndex > array_search('pending', $statusOrder) ? ' (khóa)' : '' }}
                                </option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }} {{ $currentIndex !== false && $currentIndex > array_search('confirmed', $statusOrder) ? 'disabled' : '' }}>
                                    Đã xác nhận{{ $currentIndex !== false && $currentIndex > array_search('confirmed', $statusOrder) ? ' (khóa)' : '' }}
                                </option>
                                <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }} {{ $currentIndex !== false && $currentIndex > array_search('shipping', $statusOrder) ? 'disabled' : '' }}>
                                    Đang giao{{ $currentIndex !== false && $currentIndex > array_search('shipping', $statusOrder) ? ' (khóa)' : '' }}
                                </option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>

                        <!-- Nút lưu hành động -->
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-save mr-1"></i> Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.orders') }}" class="btn btn-light btn-block text-muted py-2 border">
                                Hủy bỏ
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>

</div>

@endsection