@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <h3>Thêm Mã Giảm Giá Mới</h3>
    <div class="card shadow mt-3">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Mã Code (Ví dụ: TET2026)</label>
                        <input type="text" name="code" class="form-control" required style="text-transform: uppercase;">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Loại giảm giá</label>
                        <select name="type" class="form-control" required>
                            <option value="fixed">Giảm theo số tiền (VNĐ)</option>
                            <option value="percent">Giảm theo phần trăm (%)</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Mức giảm</label>
                        <input type="number" name="discount_value" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Giảm tối đa (Chỉ dùng nếu chọn Giảm theo %)</label>
                        <input type="number" name="max_discount_value" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Đơn hàng tối thiểu để áp dụng</label>
                        <input type="number" name="min_order_value" class="form-control" value="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Số lượng giới hạn (Để trống nếu ko giới hạn)</label>
                        <input type="number" name="usage_limit" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success mt-3">Lưu Mã Giảm Giá</button>
            </form>
        </div>
    </div>
</div>
@endsection