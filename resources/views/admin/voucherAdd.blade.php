@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <h3>Thêm Mã Giảm Giá Mới</h3>
    <div class="card shadow mt-3">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Mã Code -->
                    <div class="col-md-6 mb-3">
                        <label>Mã Code (Ví dụ: TET2026)</label>
                        <input type="text" name="code" class="form-control" style="text-transform: uppercase;" value="{{ old('code') }}">
                        @error('code')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Loại giảm giá -->
                    <div class="col-md-3 mb-3">
                        <label>Loại giảm giá</label>
                        <select name="type" class="form-control">
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Giảm theo số tiền (VNĐ)</option>
                            <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        </select>
                        @error('type')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mức giảm -->
                    <div class="col-md-3 mb-3">
                        <label>Mức giảm</label>
                        <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}">
                        @error('discount_value')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Giảm tối đa -->
                    <div class="col-md-6 mb-3">
                        <label>Giảm tối đa (Chỉ dùng nếu chọn Giảm theo %)</label>
                        <input type="number" name="max_discount_value" class="form-control" value="{{ old('max_discount_value') }}">
                        @error('max_discount_value')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Đơn tối thiểu -->
                    <div class="col-md-6 mb-3">
                        <label>Đơn hàng tối thiểu để áp dụng</label>
                        <!-- Mặc định là 0 nếu chưa nhập gì -->
                        <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value', 0) }}">
                        @error('min_order_value')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Số lượng giới hạn -->
                    <div class="col-md-4 mb-3">
                        <label>Số lượng giới hạn (Để trống nếu ko giới hạn)</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}">
                        @error('usage_limit')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Ngày bắt đầu -->
                    <div class="col-md-4 mb-3">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        @error('start_date')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Ngày kết thúc -->
                    <div class="col-md-4 mb-3">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        @error('end_date')
                            <span class="text-danger" style="font-size: 13px; font-style: italic;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success mt-3">Lưu Mã Giảm Giá</button>
            </form>
        </div>
    </div>
</div>
@endsection