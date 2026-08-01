@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Quản lý đơn hàng</h1>
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">

        <!-- Header chứa Form Tìm kiếm & Lọc -->
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-shopping-cart mr-2"></i> Danh sách đơn hàng
            </h6>

            <form method="GET"action="{{ route('admin.orders') }}" class="form-inline">
                <!-- Ô tìm kiếm SĐT -->
                <div class="position-relative search-box mr-2 mb-2 mb-md-0">
                    <input type="text"
                        id="admin-search"
                        name="keyword"
                        class="form-control form-control-sm"
                        placeholder="Nhập số điện thoại..."
                        autocomplete="off"
                        value="{{ request('keyword') }}">
                    <div id="search-order-result"></div>
                </div>

                <!-- Lọc trạng thái -->
                <select name="status" class="form-control form-control-sm mr-2 mb-2 mb-md-0">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>

                <button type="submit" class="btn btn-primary btn-sm mb-2 mb-md-0 px-3">
                    <i class="fas fa-search mr-1"></i> Lọc
                </button>
            </form>
        </div>

        <div class="card-body px-0 pb-0">

            <!-- Thông báo thành công / lỗi -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 pl-4">ID</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Tổng tiền</th>
                            <th class="py-3">Trạng thái</th>
                            <th class="py-3">Ngày đặt</th>
                            <th class="py-3 text-center pr-4">Hành động</th>
                        </tr>
                    </thead>

                    <tbody id="orders-table">
                    @forelse($orders as $order)
                        <tr class="clickable-row" data-href="{{ route('admin.orders.edit', $order->id) }}" style="cursor: pointer;">
                            
                            <td class="pl-4 font-weight-bold text-primary">#{{ $order->id }}</td>

                            <td>
                                <span class="font-weight-bold text-dark">
                                    {{ $order->shipping_name ?? $order->user->name ?? 'Null' }}
                                </span>
                            </td>

                            <td class="text-muted">{{ $order->billing_email ?? $order->user->email ?? 'Null' }}</td>

                            <td>
                                <span class="font-weight-bold text-danger">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                </span>
                            </td>

                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge badge-warning px-2 py-1">Chờ xử lý</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge badge-info px-2 py-1">Đã xác nhận</span>
                                        @break
                                    @case('shipping')
                                        <span class="badge badge-primary px-2 py-1">Đang giao</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success px-2 py-1">Hoàn thành</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger px-2 py-1">Đã hủy</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary px-2 py-1">Không rõ</span>
                                @endswitch
                            </td>

                            <td class="text-muted small">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td class="text-center pr-4">
                                <div>
                                    <!-- Nút Xem chi tiết -->
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="btn btn-sm btn-info text-white mr-1" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Nút Sửa -->
                                    <a href="{{ route('admin.orders.edit', $order->id) }}"
                                        class="btn btn-sm btn-success text-white mr-1" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Nút Xóa -->
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                        method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #{{ $order->id }}?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Không tìm thấy đơn hàng nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

</div>

<!-- JS phần tìm kiếm đơn hàng AJAX và Click chuyển trang -->
@push('scripts')
<script>
const searchUrl = "{{ route('admin.orders.search') }}";
const searchField = "shipping_phone";

document.addEventListener("DOMContentLoaded", function() {
    // Cho phép click vào toàn bộ dòng tr để chuyển sang trang chi tiết
    const rows = document.querySelectorAll(".clickable-row");
    rows.forEach(row => {
        row.addEventListener("click", function(e) {
            // Nếu click vào nút bấm, thẻ a hoặc form hành động thì không chuyển trang dòng
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) {
                return;
            }
            window.location = this.dataset.href;
        });
    });
});
</script>

<script src="{{ asset('js/admin-search.js') }}"></script>
@endpush

@endsection