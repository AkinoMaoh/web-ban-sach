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
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Cập nhật đơn hàng <span class="text-primary">{{ $order->order_number ?? '#'.$order->id }}</span></h1>
        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <!-- THÔNG BÁO THÀNH CÔNG -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <strong>Thành công!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <!-- NẾU ĐƠN HÀNG ĐÃ BỊ HỦY -> HIỂN THỊ CHUNG 1 KHUNG -->
    @if ($order->status === 'cancelled')
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <h6 class="font-weight-bold mb-1"><i class="fas fa-times-circle mr-1"></i> Đơn hàng đã bị hủy</h6>
            <p class="mb-0"><strong>Lý do:</strong> {{ $order->cancel_reason ?? 'Không có lý do cụ thể' }}</p>
            
            <!-- Nhúng trực tiếp chi tiết kỹ thuật của Admin vào chung một hộp -->
            @if (session('error'))
                <hr class="my-2 border-danger" style="opacity: 0.3;">
                <p class="mb-0 text-dark" style="font-size: 0.9rem;">
                    <i class="fas fa-info-circle mr-1"></i> <strong>Chi tiết nội bộ (Chỉ Admin):</strong> {{ session('error') }}
                </p>
            @endif
        </div>
    @endif

    <!-- CHỈ HIỆN KHUNG ERROR RỜI NẾU ĐƠN HÀNG CHƯA BỊ HỦY (Tránh lặp thông báo) -->
    @if (session('error') && $order->status !== 'cancelled')
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
        </div>
    @endif

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
                                                <strong class="text-dark">{{ $detail->product_name ?? 'Sản phẩm không xác định' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Phiên bản: {{ $detail->variant_name ?? 'Mặc định' }}
                                                </small>
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
                
                <!-- Thẻ cập nhật trạng thái -->
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
                                <span>Thanh toán:</span>
                                <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'refunded' ? 'info' : 'warning') }} px-2 py-1">
                                    {{ strtoupper($order->payment_status ?? 'unpaid') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted small">
                                <span>Ngày đặt:</span>
                                <span>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($order->subtotal_amount ?: ($order->total_amount - ($order->shipping_fee ?? 0) + $order->discount_amount), 0, ',', '.') }} đ</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Voucher {{ $order->voucher_code ? '(' . $order->voucher_code . ')' : '' }}:</span>
                                    <span class="font-weight-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Phí vận chuyển:</span>
                                <span>{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} đ</span>
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
                            
                            <select class="form-control" name="status" id="status" {{ in_array($order->status, ['cancelled', 'completed']) ? 'disabled' : '' }} required>
                                
                                @php $pendingIdx = array_search('pending', $statusOrder); @endphp
                                <option value="pending" 
                                    {{ $order->status == 'pending' ? 'selected' : '' }} 
                                    {{ $currentIndex !== false && ($currentIndex > $pendingIdx || $pendingIdx - $currentIndex > 1) ? 'disabled' : '' }}>
                                    Chờ xử lý{{ $currentIndex !== false && ($currentIndex > $pendingIdx || $pendingIdx - $currentIndex > 1) ? ' (khóa)' : '' }}
                                </option>
                                
                                @php $confirmedIdx = array_search('confirmed', $statusOrder); @endphp
                                <option value="confirmed" 
                                    {{ $order->status == 'confirmed' ? 'selected' : '' }} 
                                    {{ $currentIndex !== false && ($currentIndex > $confirmedIdx || $confirmedIdx - $currentIndex > 1) ? 'disabled' : '' }}>
                                    Đã xác nhận{{ $currentIndex !== false && ($currentIndex > $confirmedIdx || $confirmedIdx - $currentIndex > 1) ? ' (khóa)' : '' }}
                                </option>
                                
                                @php $shippingIdx = array_search('shipping', $statusOrder); @endphp
                                <option value="shipping" 
                                    {{ $order->status == 'shipping' ? 'selected' : '' }} 
                                    {{ $currentIndex !== false && ($currentIndex > $shippingIdx || $shippingIdx - $currentIndex > 1) ? 'disabled' : '' }}>
                                    Đang giao{{ $currentIndex !== false && ($currentIndex > $shippingIdx || $shippingIdx - $currentIndex > 1) ? ' (khóa)' : '' }}
                                </option>
                                
                                @php $completedIdx = array_search('completed', $statusOrder); @endphp
                                <option value="completed" 
                                    {{ $order->status == 'completed' ? 'selected' : '' }}
                                    {{ $currentIndex !== false && ($currentIndex > $completedIdx || $completedIdx - $currentIndex > 1) ? 'disabled' : '' }}>
                                    Hoàn thành{{ $currentIndex !== false && ($currentIndex > $completedIdx || $completedIdx - $currentIndex > 1) ? ' (khóa)' : '' }}
                                </option>
                                
                                <option value="cancelled" 
                                    {{ $order->status == 'cancelled' ? 'selected' : '' }}
                                    {{ in_array($order->status, ['shipping', 'completed']) ? 'disabled' : '' }}>
                                    Đã hủy{{ in_array($order->status, ['shipping', 'completed']) ? ' (khóa)' : '' }}
                                </option>
                            </select>
                            
                            @if(in_array($order->status, ['cancelled', 'completed']))
                                <input type="hidden" name="status" value="{{ $order->status }}">
                            @endif
                        </div>

                        <!-- KHU VỰC HIỂN THỊ LÝ DO HỦY ĐƠN VÀ NHẬP MỚI KHI HỦY -->
                        @if($order->status === 'cancelled')
                            <!-- NẾU TRẠNG THÁI HIỆN TẠI LÀ ĐÃ HỦY: Hiện khung đọc lý do -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-danger small text-uppercase">
                                    Lý do hủy đơn
                                </label>
                                <textarea class="form-control bg-light text-danger font-italic" rows="3" disabled>{{ $order->cancel_reason ?? 'Không có lý do cụ thể' }}</textarea>
                            </div>
                        @elseif($order->status !== 'completed')
                            <!-- NẾU TRẠNG THÁI LÀ BÌNH THƯỜNG: Hiện khung nhập form ẩn (chỉ hiện khi user chuyển select sang Đã hủy) -->
                            <div class="form-group mb-4" id="cancel_reason_group" style="display: none;">
                                <label for="cancel_reason" class="font-weight-bold text-danger small text-uppercase">
                                    Lý do hủy đơn <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="cancel_reason" id="cancel_reason" rows="3" placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                            </div>
                        @endif

                        <!-- Nút lưu hành động -->
                        <div class="d-flex flex-column">
                            @if(!in_array($order->status, ['cancelled', 'completed']))
                                <button type="submit" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                                    <i class="fas fa-save mr-1"></i> Lưu thay đổi
                                </button>
                            @endif
                            <a href="{{ route('admin.orders') }}" class="btn btn-light btn-block text-muted py-2 border">
                                {{ in_array($order->status, ['cancelled', 'completed']) ? 'Trở về danh sách' : 'Hủy bỏ' }}
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>

    @if(in_array($order->refund_status, ['requested', 'pending'], true))
        <div class="card border-warning shadow-sm mb-4">
            <div class="card-header bg-warning text-dark font-weight-bold">
                <i class="fas fa-undo-alt mr-2"></i>Yêu cầu hoàn tiền đang chờ xử lý
            </div>
            <div class="card-body">
                <p><strong>Lý do:</strong> {{ $order->cancel_request_reason ?? 'Không có lý do cụ thể.' }}</p>
                <p class="text-danger">
                    Hệ thống không tự gọi API hoàn tiền VNPAY. Chỉ bấm xác nhận sau khi bạn đã hoàn tiền thực tế trên cổng thanh toán.
                </p>
                <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn giao dịch đã được hoàn tiền thực tế?');">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="refund_reference" class="font-weight-bold">Mã tham chiếu hoàn tiền</label>
                        <input type="text" class="form-control" id="refund_reference" name="refund_reference" maxlength="100" placeholder="Nhập mã từ VNPAY hoặc hệ thống đối soát">
                    </div>
                    <button type="submit" class="btn btn-warning font-weight-bold">
                        <i class="fas fa-check mr-1"></i>Xác nhận đã hoàn tiền
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>

<!-- SCRIPT ẨN HIỆN TEXTAREA KHI CHỌN HỦY -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.getElementById('status');
        const cancelReasonGroup = document.getElementById('cancel_reason_group');
        const cancelReasonInput = document.getElementById('cancel_reason');

        function toggleCancelReason() {
            if (!cancelReasonGroup) return; // Tránh lỗi nếu đơn đã hoàn thành/hủy (div không tồn tại)

            if (statusSelect.value === 'cancelled') {
                cancelReasonGroup.style.display = 'block';
                cancelReasonInput.setAttribute('required', 'required'); // Bắt buộc nhập khi Hủy
            } else {
                cancelReasonGroup.style.display = 'none';
                cancelReasonInput.removeAttribute('required'); // Xóa bắt buộc nhập nếu chọn trạng thái khác
                cancelReasonInput.value = ''; // Xóa chữ lỡ nhập
            }
        }

        // Chạy lần đầu khi load trang
        if(statusSelect) toggleCancelReason();

        // Chạy mỗi khi đổi trạng thái
        if(statusSelect) {
            statusSelect.addEventListener('change', toggleCancelReason);
        }
    });
</script>
@endsection
