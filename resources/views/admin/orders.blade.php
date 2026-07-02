@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Trang quản lý đơn hàng</h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Dữ liệu đơn hàng</h6>

            <form method="GET" action="{{ route('admin.orders') }}" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending"   {{ request('status') == 'pending'    ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="confirmed" {{ request('status') == 'confirmed'  ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping"  {{ request('status') == 'shipping'   ? 'selected' : '' }}>Đang giao</option>
                    <option value="completed" {{ request('status') == 'completed'  ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled'  ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </form>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Email</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($orders as $order)
                        <tr>

                            <td>{{ $order->id }}</td>

                            <td>{{ $order->shipping_name ?? $order->user->name ?? 'Null' }}</td>

                            <td>{{ $order->billing_email ?? $order->user->email ?? 'Null' }}</td>

                            <td>
                                {{ number_format($order->total_amount, 0, ',', '.') }} đ
                            </td>

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

                            <td>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="btn btn-sm btn-info">
                                    Chi tiết
                                </a>

                                <a href="{{ route('admin.orders.edit', $order->id) }}"
                                    class="btn btn-sm btn-success">
                                    Cập nhật
                                </a>

                                <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #{{ $order->id }}?')">
                                        Xóa
                                    </button>

                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có đơn hàng nào.</td>
                        </tr>
                    @endforelse

                    </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

</div>

@endsection