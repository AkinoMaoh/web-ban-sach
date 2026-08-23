@extends('layout.user')

@section('content')
{{-- Xóa dòng @vite và thay bằng 3 dòng dưới đây --}}
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/checkout.js') }}"></script>

<div class="checkout-breadcrumb py-3 mb-4 shadow-sm border-bottom">
        <div class="container">
<!-- ... phần còn lại giữ nguyên ... -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-muted">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
    </div>
</div>

<section class="checkout-section pb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="serif-font font-weight-bold">Hoàn tất đơn hàng</h2>
            <p class="text-muted">Thông tin bạn nhập sẽ được giữ lại nếu cần sửa lỗi hoặc tải lại trang.</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger checkout-alert" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger checkout-alert" role="alert">
                <strong>Vui lòng kiểm tra lại thông tin:</strong>
                <ul class="mb-0 mt-2 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('checkout.process') }}"
            method="POST"
            id="checkoutForm"
            data-csrf="{{ csrf_token() }}"
            data-fee-url="{{ route('payment.calculate_fee') }}"
            data-voucher-url="{{ route('checkout.apply_voucher') }}"
            data-district-url="{{ url('/api/locations/districts') }}"
            data-ward-url="{{ url('/api/locations/wards') }}"
            data-has-old-input="{{ session()->hasOldInput() ? '1' : '0' }}"
        >
            @csrf
            <input type="hidden" name="checkout_token" id="checkout_token" value="{{ $checkoutToken }}">
            <input type="hidden" name="shipping_quote_token" id="shipping_quote_token" value="">
            <input type="hidden" name="applied_voucher" id="applied_voucher_input" value="{{ old('applied_voucher') }}">
            <input type="hidden" name="address_id" id="address_id" value="{{ old('address_id') }}">

            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="checkout-card">
                        <h4 class="checkout-heading">1. Thông tin giao hàng</h4>

                        <div class="form-group">
                            <label for="shipping_name" class="font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input
                                id="shipping_name"
                                type="text"
                                name="shipping_name"
                                value="{{ old('shipping_name', $defaultAddress->receiver_name ?? (auth()->user()->name ?? '')) }}"
                                class="form-control custom-input @error('shipping_name') is-invalid @enderror"
                                maxlength="255"
                                autocomplete="name"
                                required
                            >
                            @error('shipping_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="billing_email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input
                                    id="billing_email"
                                    type="email"
                                    name="billing_email"
                                    value="{{ old('billing_email', auth()->user()->email ?? '') }}"
                                    class="form-control custom-input @error('billing_email') is-invalid @enderror"
                                    maxlength="255"
                                    autocomplete="email"
                                    required
                                >
                                @error('billing_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="shipping_phone" class="font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input
                                    id="shipping_phone"
                                    type="tel"
                                    name="shipping_phone"
                                    value="{{ old('shipping_phone', $defaultAddress->receiver_phone ?? (auth()->user()->phone ?? '')) }}"
                                    class="form-control custom-input @error('shipping_phone') is-invalid @enderror"
                                    maxlength="20"
                                    inputmode="tel"
                                    autocomplete="tel"
                                    required
                                >
                                @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @auth
                            @if($addresses->isNotEmpty())
                                <div class="form-group">
                                    <label for="saved_address" class="font-weight-bold">Địa chỉ đã lưu</label>
                                    <select id="saved_address" class="custom-select custom-input">
                                        <option value="">Nhập địa chỉ khác</option>
                                        @foreach($addresses as $address)
                                            <option
                                                value="{{ $address->id }}"
                                                data-name="{{ $address->receiver_name }}"
                                                data-phone="{{ $address->receiver_phone }}"
                                                data-province="{{ $address->province_id }}"
                                                data-district="{{ $address->district_id }}"
                                                data-ward="{{ $address->ward_code }}"
                                                data-street="{{ $address->specific_address }}"
                                                @selected((string) old('address_id') === (string) $address->id)
                                            >
                                                {{ $address->is_default ? 'Mặc định · ' : '' }}{{ $address->receiver_name }} — {{ $address->specific_address }}, {{ $address->ward?->name }}, {{ $address->district?->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @endauth

                        <h5 class="font-weight-bold mt-3 mb-3">Địa chỉ chi tiết</h5>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="province" class="checkout-label">Tỉnh / Thành <span class="text-danger">*</span></label>
                                <select id="province" name="province_id" class="custom-select custom-input @error('province_id') is-invalid @enderror" required>
                                    <option value="">Chọn Tỉnh/Thành</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}" @selected((string) old('province_id', $defaultAddress->province_id ?? '') === (string) $province->id)>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="district" class="checkout-label">Quận / Huyện <span class="text-danger">*</span></label>
                                <select
                                    id="district"
                                    name="district_id"
                                    class="custom-select custom-input @error('district_id') is-invalid @enderror"
                                    data-selected="{{ old('district_id', $defaultAddress->district_id ?? '') }}"
                                    required
                                >
                                    <option value="">Chọn Quận/Huyện</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="ward" class="checkout-label">Phường / Xã <span class="text-danger">*</span></label>
                                <select
                                    id="ward"
                                    name="ward_code"
                                    class="custom-select custom-input @error('ward_code') is-invalid @enderror"
                                    data-selected="{{ old('ward_code', $defaultAddress->ward_code ?? '') }}"
                                    required
                                >
                                    <option value="">Chọn Phường/Xã</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="specific_address" class="checkout-label">Số nhà, tên đường <span class="text-danger">*</span></label>
                            <input
                                id="specific_address"
                                type="text"
                                name="specific_address"
                                value="{{ old('specific_address', $defaultAddress->specific_address ?? '') }}"
                                class="form-control custom-input @error('specific_address') is-invalid @enderror"
                                maxlength="500"
                                autocomplete="street-address"
                                required
                            >
                            @error('specific_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @auth
                            <div class="address-options p-3 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="save_address" name="save_address" value="1" @checked(old('save_address'))>
                                    <label class="custom-control-label" for="save_address">Lưu địa chỉ này cho lần sau</label>
                                </div>
                                <div class="custom-control custom-checkbox mt-2" id="default_address_wrap">
                                    <input type="checkbox" class="custom-control-input" id="set_default_address" name="set_default_address" value="1" @checked(old('set_default_address'))>
                                    <label class="custom-control-label" for="set_default_address">Đặt làm địa chỉ mặc định</label>
                                </div>
                            </div>
                        @endauth

                        <div class="form-group">
                            <label for="order_notes" class="font-weight-bold">Ghi chú đơn hàng</label>
                            <textarea id="order_notes" name="order_notes" rows="3" maxlength="2000" class="form-control custom-input" placeholder="Thời gian giao phù hợp, chỉ dẫn địa chỉ...">{{ old('order_notes') }}</textarea>
                        </div>

                        <h4 class="checkout-heading mt-5">2. Phương thức thanh toán</h4>
                        <label class="payment-method-card mb-3 w-100">
                            <input type="radio" name="payment_method" value="cod" @checked(old('payment_method', 'cod') === 'cod')>
                            <span class="payment-card-content d-flex align-items-center">
                                <i class="fas fa-money-bill-wave payment-icon text-success"></i>
                                <span><strong>Thanh toán khi nhận hàng (COD)</strong><small>Thanh toán cho shipper khi nhận sách.</small></span>
                            </span>
                        </label>
                        <label class="payment-method-card w-100">
                            <input type="radio" name="payment_method" value="vnpay" @checked(old('payment_method') === 'vnpay')>
                            <span class="payment-card-content d-flex align-items-center">
                                <i class="fas fa-credit-card payment-icon text-primary"></i>
                                <span><strong>Thanh toán trực tuyến VNPAY</strong><small>Thẻ ATM, Internet Banking hoặc ví VNPAY.</small></span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="checkout-summary sticky-top">
                        <h4 class="checkout-heading">Đơn hàng của bạn</h4>
                        <div class="order-items-list mb-3">
                            @forelse($cart as $id => $details)
                                <div class="order-line">
                                    <div class="pr-3">
                                        <strong>{{ $details['name'] ?? 'Sách ID '.$id }}</strong>
                                        <small>Số lượng: {{ $details['quantity'] }}</small>
                                    </div>
                                    <span>{{ number_format($details['price'] * $details['quantity']) }}đ</span>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">Giỏ hàng rỗng.</p>
                            @endforelse
                        </div>

                        <div class="voucher-section">
                            <label for="voucher_code" class="font-weight-bold">Mã giảm giá</label>
                            <div class="input-group">
                                <input type="text" id="voucher_code" class="form-control custom-input text-uppercase" value="{{ old('applied_voucher') }}" maxlength="100" autocomplete="off" placeholder="Nhập hoặc chọn mã">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#voucherModal">Chọn</button>
                                    <button type="button" id="btn-apply-voucher" class="btn btn-dark">Áp dụng</button>
                                </div>
                            </div>
                            <button type="button" id="btn-remove-voucher" class="btn btn-link text-danger px-0 d-none">Bỏ mã đang dùng</button>
                            <small id="voucher-message" class="d-block font-weight-bold" aria-live="polite"></small>
                        </div>

                        <div class="summary-line">
                            <span>Tạm tính</span>
                            <span id="subtotal_text" data-value="{{ $totalAmount ?? 0 }}">{{ number_format($totalAmount ?? 0) }}đ</span>
                        </div>
                        <div class="summary-line text-success d-none" id="discount-row">
                            <span>Giảm giá</span>
                            <strong id="discount_amount_text">-0đ</strong>
                        </div>
                        <div class="summary-line">
                            <span>Phí vận chuyển</span>
                            <span id="shipping_fee_text">Vui lòng chọn địa chỉ</span>
                        </div>
                        <div class="summary-total">
                            <span>Tổng cộng</span>
                            <strong id="total_amount_text">{{ number_format($totalAmount ?? 0) }}đ</strong>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="agree_terms" name="agree_terms" value="1" @checked(old('agree_terms')) required>
                            <label class="custom-control-label small" for="agree_terms">Tôi xác nhận thông tin và đồng ý với chính sách đặt hàng.</label>
                        </div>

                        <button type="submit" id="checkout-submit" class="btn btn-orange btn-block rounded-pill py-3 font-weight-bold text-uppercase">
                            <span class="submit-label"><i class="fas fa-lock mr-2"></i>Đặt hàng an toàn</span>
                            <span class="submit-loading d-none"><i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...</span>
                        </button>
                        <small class="text-muted d-block text-center mt-2"><i class="fas fa-shield-alt mr-1"></i>Giá và tồn kho được kiểm tra lại trên máy chủ.</small>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="voucherModalLabel">Chọn mã giảm giá</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body voucher-modal-body">
                    @forelse($vouchers as $voucher)
                    @php($isEligible = ($totalAmount ?? 0) >= (float) $voucher->min_order_value)
                    <div class="voucher-card">
                        <div class="voucher-code">{{ $voucher->code }}</div>
                        <div class="flex-grow-1 px-3">
                            <strong>
                                {{ $voucher->type === 'percent' ? 'Giảm '.$voucher->discount_value.'%' : 'Giảm '.number_format($voucher->discount_value).'đ' }}
                            </strong>
                            <small>Đơn tối thiểu {{ number_format($voucher->min_order_value) }}đ · HSD {{ $voucher->end_date?->format('d/m/Y') ?? 'không giới hạn' }}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-select-voucher" data-code="{{ $voucher->code }}" @disabled(!$isEligible)>
                            {{ $isEligible ? 'Chọn' : 'Chưa đủ' }}
                        </button>
                    </div>
                @empty
                    <p class="text-center text-muted my-4">Hiện chưa có mã phù hợp.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
