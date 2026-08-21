@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Quản lý mã giảm giá</h3>
            <p class="text-muted mb-0">Theo dõi điều kiện, lượt dùng và trạng thái của từng voucher.</p>
        </div>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary mt-2 mt-md-0">
            <i class="fas fa-plus mr-1"></i> Thêm mã mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        @foreach([
            ['label' => 'Tổng voucher', 'value' => $stats['total'], 'color' => 'primary', 'icon' => 'ticket-alt'],
            ['label' => 'Đang hoạt động', 'value' => $stats['active'], 'color' => 'success', 'icon' => 'check-circle'],
            ['label' => 'Sắp diễn ra', 'value' => $stats['upcoming'], 'color' => 'warning', 'icon' => 'clock'],
            ['label' => 'Đã lưu trữ', 'value' => $stats['archived'], 'color' => 'secondary', 'icon' => 'archive'],
        ] as $stat)
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-{{ $stat['color'] }} h-100 py-2">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-{{ $stat['color'] }} text-uppercase mb-1">{{ $stat['label'] }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stat['value'] }}</div>
                            </div>
                            <i class="fas fa-{{ $stat['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.vouchers.index') }}" class="row align-items-end mb-4">
                <div class="col-md-5 mb-2">
                    <label class="small font-weight-bold">Tìm theo mã hoặc tên</label>
                    <input type="search" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Nhập mã voucher...">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Loại</label>
                    <select name="type" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="fixed" @selected(request('type') === 'fixed')>Tiền cố định</option>
                        <option value="percent" @selected(request('type') === 'percent')>Phần trăm</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="active" @selected(request('status') === 'active')>Đang hoạt động</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Đã tắt</option>
                        <option value="upcoming" @selected(request('status') === 'upcoming')>Chưa bắt đầu</option>
                        <option value="expired" @selected(request('status') === 'expired')>Đã hết hạn</option>
                        <option value="exhausted" @selected(request('status') === 'exhausted')>Đã hết lượt</option>
                        <option value="archived" @selected(request('status') === 'archived')>Đã lưu trữ</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 d-flex">
                    <button class="btn btn-primary flex-grow-1"><i class="fas fa-filter mr-1"></i> Lọc</button>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-light ml-1" title="Xóa bộ lọc"><i class="fas fa-times"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Voucher</th>
                            <th>Mức giảm</th>
                            <th>Điều kiện</th>
                            <th>Lượt dùng</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th style="min-width: 190px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $item)
                            @php
                                $badgeClasses = [
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'upcoming' => 'info',
                                    'expired' => 'danger',
                                    'exhausted' => 'warning',
                                    'archived' => 'dark',
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $item->code }}</strong>
                                    <small class="d-block text-muted">{{ $item->name ?: 'Chưa đặt tên' }}</small>
                                    @unless($item->is_public)
                                        <span class="badge badge-light"><i class="fas fa-eye-slash mr-1"></i>Mã riêng tư</span>
                                    @endunless
                                </td>
                                <td>
                                    @if($item->type === 'percent')
                                        <strong>{{ number_format($item->discount_value, 0) }}%</strong>
                                        <small class="d-block text-muted">Tối đa {{ number_format($item->max_discount_value, 0, ',', '.') }}đ</small>
                                    @else
                                        <strong>{{ number_format($item->discount_value, 0, ',', '.') }}đ</strong>
                                    @endif
                                </td>
                                <td>
                                    Đơn từ {{ number_format($item->min_order_value, 0, ',', '.') }}đ
                                    <small class="d-block text-muted">Mỗi khách: {{ $item->usage_limit_per_customer ?? '∞' }} lượt</small>
                                </td>
                                <td>
                                    <strong>{{ $item->used_count }}</strong> / {{ $item->usage_limit ?? '∞' }}
                                    @if($item->usage_limit)
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" style="width: {{ min(($item->used_count / $item->usage_limit) * 100, 100) }}%"></div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->start_date?->format('d/m/Y') }}
                                    <small class="d-block text-muted">đến {{ $item->end_date?->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $badgeClasses[$item->status_code] ?? 'secondary' }}">{{ $item->status_label }}</span>
                                </td>
                                <td>
                                    @if($item->trashed())
                                        <form action="{{ route('admin.vouchers.restore', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success"><i class="fas fa-undo mr-1"></i>Khôi phục</button>
                                        </form>
                                    @else
                                        <div class="d-flex flex-wrap" style="gap: 4px;">
                                            <a href="{{ route('admin.vouchers.show', $item) }}" class="btn btn-sm btn-outline-info" title="Chi tiết"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.vouchers.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.vouchers.toggle', $item) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-outline-{{ $item->is_active ? 'warning' : 'success' }}" title="{{ $item->is_active ? 'Tạm khóa' : 'Bật' }}">
                                                    <i class="fas fa-{{ $item->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.vouchers.destroy', $item) }}" method="POST" onsubmit="return confirm('Lưu trữ voucher này? Lịch sử sử dụng sẽ được giữ lại.');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Lưu trữ"><i class="fas fa-archive"></i></button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">Không tìm thấy voucher phù hợp.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $vouchers->links() }}</div>
        </div>
    </div>
</div>
@endsection
