@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Chỉnh sửa {{ $voucher->code }}</h3>
            <p class="text-muted mb-0">Các đơn cũ vẫn giữ nguyên số tiền đã giảm tại thời điểm đặt hàng.</p>
        </div>
        <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại chi tiết
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.partials.voucherForm', ['voucher' => $voucher])

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-light mr-2">Hủy</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
