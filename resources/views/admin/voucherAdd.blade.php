@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Thêm mã giảm giá</h3>
            <p class="text-muted mb-0">Thiết lập đầy đủ điều kiện trước khi công khai voucher.</p>
        </div>
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                @include('admin.partials.voucherForm')

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-light mr-2">Hủy</a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save mr-1"></i> Lưu mã giảm giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
