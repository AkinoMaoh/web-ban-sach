@php
    $voucher = $voucher ?? null;
    $activeValue = (bool) old('is_active', $voucher?->is_active ?? true);
    $publicValue = (bool) old('is_public', $voucher?->is_public ?? true);
@endphp

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="code" class="font-weight-bold">Mã voucher <span class="text-danger">*</span></label>
        <input
            type="text"
            id="code"
            name="code"
            class="form-control text-uppercase @error('code') is-invalid @enderror"
            value="{{ old('code', $voucher?->code) }}"
            maxlength="100"
            placeholder="Ví dụ: SACHMOI20"
            required
        >
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8 mb-3">
        <label for="name" class="font-weight-bold">Tên chương trình <span class="text-danger">*</span></label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $voucher?->name) }}"
            maxlength="150"
            placeholder="Ví dụ: Khuyến mãi sách mới tháng 8"
            required
        >
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="description" class="font-weight-bold">Mô tả</label>
        <textarea
            id="description"
            name="description"
            rows="2"
            maxlength="2000"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Điều kiện hoặc ghi chú ngắn dành cho khách hàng"
        >{{ old('description', $voucher?->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="type" class="font-weight-bold">Loại giảm giá <span class="text-danger">*</span></label>
        <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
            <option value="fixed" @selected(old('type', $voucher?->type ?? 'fixed') === 'fixed')>Số tiền cố định (VNĐ)</option>
            <option value="percent" @selected(old('type', $voucher?->type) === 'percent')>Phần trăm (%)</option>
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="discount_value" class="font-weight-bold">Mức giảm <span class="text-danger">*</span></label>
        <input
            type="number"
            id="discount_value"
            name="discount_value"
            class="form-control @error('discount_value') is-invalid @enderror"
            value="{{ old('discount_value', $voucher?->discount_value) }}"
            min="1"
            step="1"
            required
        >
        @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3" id="max-discount-group">
        <label for="max_discount_value" class="font-weight-bold">Giảm tối đa (VNĐ) <span class="text-danger">*</span></label>
        <input
            type="number"
            id="max_discount_value"
            name="max_discount_value"
            class="form-control @error('max_discount_value') is-invalid @enderror"
            value="{{ old('max_discount_value', $voucher?->max_discount_value) }}"
            min="1"
            step="1000"
        >
        @error('max_discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="min_order_value" class="font-weight-bold">Đơn hàng tối thiểu (VNĐ)</label>
        <input
            type="number"
            id="min_order_value"
            name="min_order_value"
            class="form-control @error('min_order_value') is-invalid @enderror"
            value="{{ old('min_order_value', $voucher?->min_order_value ?? 0) }}"
            min="0"
            step="1000"
            required
        >
        @error('min_order_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="usage_limit" class="font-weight-bold">Tổng lượt sử dụng</label>
        <input
            type="number"
            id="usage_limit"
            name="usage_limit"
            class="form-control @error('usage_limit') is-invalid @enderror"
            value="{{ old('usage_limit', $voucher?->usage_limit) }}"
            min="1"
            placeholder="Để trống = không giới hạn"
        >
        @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="usage_limit_per_customer" class="font-weight-bold">Lượt dùng mỗi khách</label>
        <input
            type="number"
            id="usage_limit_per_customer"
            name="usage_limit_per_customer"
            class="form-control @error('usage_limit_per_customer') is-invalid @enderror"
            value="{{ old('usage_limit_per_customer', $voucher?->usage_limit_per_customer) }}"
            min="1"
            placeholder="Để trống = không giới hạn"
        >
        @error('usage_limit_per_customer') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="start_date" class="font-weight-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
        <input
            type="date"
            id="start_date"
            name="start_date"
            class="form-control @error('start_date') is-invalid @enderror"
            value="{{ old('start_date', $voucher?->start_date?->format('Y-m-d')) }}"
            required
        >
        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_date" class="font-weight-bold">Ngày kết thúc <span class="text-danger">*</span></label>
        <input
            type="date"
            id="end_date"
            name="end_date"
            class="form-control @error('end_date') is-invalid @enderror"
            value="{{ old('end_date', $voucher?->end_date?->format('Y-m-d')) }}"
            required
        >
        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <input type="hidden" name="is_active" value="0">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked($activeValue)>
            <label class="custom-control-label font-weight-bold" for="is_active">Cho phép sử dụng voucher</label>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <input type="hidden" name="is_public" value="0">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" value="1" @checked($publicValue)>
            <label class="custom-control-label font-weight-bold" for="is_public">Hiển thị trong danh sách voucher tại checkout</label>
        </div>
        <small class="text-muted">Tắt mục này nếu chỉ muốn khách biết mã qua chiến dịch riêng.</small>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        const $type = $('#type');
        const $maxGroup = $('#max-discount-group');
        const $maxInput = $('#max_discount_value');

        function syncVoucherType() {
            const isPercent = $type.val() === 'percent';
            $maxGroup.toggle(isPercent);
            $maxInput.prop('required', isPercent);

            if (!isPercent) {
                $maxInput.val('');
            }
        }

        $('#code').on('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
        });

        $type.on('change', syncVoucherType);
        syncVoucherType();
    });
</script>
@endpush
