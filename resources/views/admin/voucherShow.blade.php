@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center">
                <h3 class="mb-1 mr-3">{{ $voucher->code }}</h3>
                <span class="badge badge-{{ $voucher->status_code === 'active' ? 'success' : ($voucher->status_code === 'inactive' ? 'secondary' : 'warning') }}">
                    {{ $voucher->status_label }}
                </span>
            </div>
            <p class="text-muted mb-0">{{ $voucher->name }}</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-light mr-2">
                <i class="fas fa-list mr-1"></i> Danh sách
            </a>
            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Chỉnh sửa
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100"><div class="card-body">
                <small class="text-muted text-uppercase">Đang giữ lượt</small>
                <h4 class="mb-0 text-warning">{{ $voucher->reserved_usages_count }}</h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100"><div class="card-body">
                <small class="text-muted text-uppercase">Đã sử dụng</small>
                <h4 class="mb-0 text-success">{{ $voucher->completed_usages_count }}</h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100"><div class="card-body">
                <small class="text-muted text-uppercase">Đã hoàn lượt</small>
                <h4 class="mb-0 text-secondary">{{ $voucher->released_usages_count }}</h4>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <small class="text-muted text-uppercase">Còn lại</small>
                <h4 class="mb-0 text-primary">{{ $voucher->remainingUses() ?? '∞' }}</h4>
            </div></div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header font-weight-bold">Điều kiện áp dụng</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">Mức giảm</small>
                    <strong>
                        @if($voucher->type === 'percent')
                            {{ number_format($voucher->discount_value, 0) }}%
                            (tối đa {{ number_format($voucher->max_discount_value, 0, ',', '.') }}đ)
                        @else
                            {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                        @endif
                    </strong>
                </div>
                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">Đơn tối thiểu</small>
                    <strong>{{ number_format($voucher->min_order_value, 0, ',', '.') }}đ</strong>
                </div>
                <div class="col-md-4 mb-3">
                    <small class="text-muted d-block">Mỗi khách</small>
                    <strong>{{ $voucher->usage_limit_per_customer ?? 'Không giới hạn' }}</strong>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <small class="text-muted d-block">Thời gian</small>
                    <strong>{{ $voucher->start_date->format('d/m/Y') }} – {{ $voucher->end_date->format('d/m/Y') }}</strong>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <small class="text-muted d-block">Hiển thị tại checkout</small>
                    <strong>{{ $voucher->is_public ? 'Có' : 'Không' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Mô tả</small>
                    <span>{{ $voucher->description ?: 'Không có' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header font-weight-bold">Lịch sử sử dụng</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Số tiền giảm</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usages as $usage)
                            <tr>
                                <td>
                                    @if($usage->order)
                                        <a href="{{ route('admin.orders.show', $usage->order_id) }}">#{{ $usage->order_id }}</a>
                                    @else
                                        #{{ $usage->order_id }}
                                    @endif
                                </td>
                                <td>{{ $usage->user?->email ?? $usage->order?->billing_email ?? 'Khách vãng lai' }}</td>
                                <td class="text-success font-weight-bold">-{{ number_format($usage->discount_amount, 0, ',', '.') }}đ</td>
                                <td>
                                    @if($usage->status === 'used')
                                        <span class="badge badge-success">Đã dùng</span>
                                    @elseif($usage->status === 'released')
                                        <span class="badge badge-secondary">Đã hoàn lượt</span>
                                    @else
                                        <span class="badge badge-warning">Đang giữ lượt</span>
                                    @endif
                                </td>
                                <td>{{ optional($usage->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Voucher chưa có lượt sử dụng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($usages->hasPages())
            <div class="card-footer">{{ $usages->links() }}</div>
        @endif
    </div>
</div>
@endsection
