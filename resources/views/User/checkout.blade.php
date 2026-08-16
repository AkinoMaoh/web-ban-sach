@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-muted">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Thanh toán</li>
            </ol>
        </nav>
    </div>
</div>

<section class="checkout-section spad mb-5 pb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="serif-font font-weight-bold" style="color: var(--text-main);">Hoàn tất đơn hàng</h2>
            <p class="text-muted">Vui lòng điền đầy đủ thông tin bên dưới để chúng tôi giao sách đến bạn.</p>
        </div>

        {{-- Hiển thị thông báo lỗi từ Controller --}}
        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-left: 5px solid #dc3545; border-radius: 6px;">
                <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Lỗi thanh toán:</strong> {{ session('error') }}
            </div>
        @endif

        {{-- Hiển thị lỗi Validate (nhập thiếu) --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-left: 5px solid #dc3545; border-radius: 6px;">
                <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Vui lòng kiểm tra lại:</h6>
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row">
                <!-- Cột Thông tin giao hàng -->
                <div class="col-lg-7 mb-4">
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                        <h4 class="serif-font font-weight-bold mb-4 border-bottom pb-3">1. Thông tin giao hàng</h4>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', $defaultAddress->receiver_name ?? (auth()->user()->name ?? '')) }}" class="form-control form-control-lg custom-input" placeholder="Nhập tên người nhận">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Email <span class="text-danger">*</span></label>
                                <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email ?? '') }}" class="form-control form-control-lg custom-input" placeholder="ten@gmail.com">
                            </div>
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', $defaultAddress->receiver_phone ?? (auth()->user()->phone ?? '')) }}" class="form-control form-control-lg custom-input" placeholder="098...">
                            </div>
                        </div>

                        <h5 class="serif-font font-weight-bold mb-3 mt-2">Địa chỉ chi tiết</h5>
                        
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label class="text-muted small font-weight-bold text-uppercase">Tỉnh / Thành <span class="text-danger">*</span></label>
                                <select id="province" class="custom-select custom-select-lg custom-input">
                                    <option value="">Chọn Tỉnh/Thành</option>
                                    @if(isset($provinces))
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" data-name="{{ $province->name }}" {{ old('province_id', $defaultAddress->province_id ?? '') == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label class="text-muted small font-weight-bold text-uppercase">Quận / Huyện <span class="text-danger">*</span></label>
                                <select id="district" class="custom-select custom-select-lg custom-input">
                                    <option value="">Chọn Quận/Huyện</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label class="text-muted small font-weight-bold text-uppercase">Phường / Xã <span class="text-danger">*</span></label>
                                <select id="ward" class="custom-select custom-select-lg custom-input">
                                    <option value="">Chọn Phường/Xã</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase">Số nhà, Tên đường <span class="text-danger">*</span></label>
                            <input type="text" id="street" class="form-control form-control-lg custom-input" placeholder="Ví dụ: 123 Đường Lê Lợi..." value="{{ old('specific_address', $defaultAddress->specific_address ?? '') }}">
                        </div>
                        
                        <!-- Hidden input chứa full địa chỉ cho Controller -->
                        <input type="hidden" name="full_address" id="full_address">

                        <div class="form-group mb-4 mt-4">
                            <label class="font-weight-bold text-dark">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea name="order_notes" rows="3" class="form-control custom-input" placeholder="Ghi chú về thời gian giao hàng, chỉ dẫn địa chỉ...">{{ old('order_notes') }}</textarea>
                        </div>

                        <h4 class="serif-font font-weight-bold mb-4 mt-5 border-bottom pb-3">2. Phương thức thanh toán</h4>
                        
                        <!-- Thanh toán COD -->
                        <label class="payment-method-card mb-3 w-100">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div class="payment-card-content d-flex align-items-center p-3 border rounded shadow-sm">
                                <img src="https://cdn-icons-png.flaticon.com/512/6491/6491490.png" width="40" class="mr-3" alt="COD">
                                <div>
                                    <h6 class="font-weight-bold mb-1 text-dark">Thanh toán khi nhận hàng (COD)</h6>
                                    <span class="text-muted small">Khách hàng thanh toán bằng tiền mặt khi shipper giao sách tới.</span>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Thanh toán VNPAY -->
                        <label class="payment-method-card w-100">
                            <input type="radio" name="payment_method" value="vnpay">
                            <div class="payment-card-content d-flex align-items-center p-3 border rounded shadow-sm">
                                <img src="https://vnpay.vn/s1/vnpay/logo.svg" width="40" class="mr-3" alt="VNPAY">
                                <div>
                                    <h6 class="font-weight-bold mb-1 text-dark">Thanh toán trực tuyến VNPAY</h6>
                                    <span class="text-muted small">Thanh toán an toàn qua thẻ ATM, Internet Banking hoặc ví VNPAY.</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Cột Tóm tắt đơn hàng -->
                <div class="col-lg-5">
                    <div class="bg-light p-4 rounded shadow-sm border sticky-top" style="top: 100px;">
                        <h4 class="serif-font font-weight-bold mb-4 border-bottom pb-3">Đơn hàng của bạn</h4>
                        
                        <div class="order-items-list mb-4" style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                            @if(!empty($cart))
                                @foreach($cart as $id => $details)
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div class="pr-3">
                                            <h6 class="mb-1 text-dark" style="font-size: 15px; line-height: 1.4;">{{ $details['name'] ?? 'Sách ID '.$id }}</h6>
                                            <span class="text-muted small">Số lượng: <strong class="text-dark">{{ $details['quantity'] }}</strong></span>
                                            
                                            @if(isset($details['original_price']) && $details['original_price'] > $details['price'])
                                                <span class="text-muted small ml-2"><del>{{ number_format($details['original_price']) }} đ</del></span>
                                            @endif
                                        </div>
                                        <span class="font-weight-bold" style="color: var(--primary-color);">{{ number_format($details['price'] * $details['quantity']) }} đ</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted text-center py-3">Giỏ hàng rỗng.</div>
                            @endif
                        </div>

                        <!-- KHU VỰC NHẬP MÃ GIẢM GIÁ -->
                        <div class="voucher-section mb-3 pt-3 border-top">
                            <div class="input-group">
                                <input type="text" id="voucher_code" class="form-control custom-input" placeholder="Nhập hoặc chọn mã..." style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#voucherModal" style="border-radius: 0;">
                                        <i class="fas fa-list mr-1"></i> Chọn mã
                                    </button>
                                    <button type="button" id="btn-apply-voucher" class="btn btn-dark" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">Áp dụng</button>
                                </div>
                            </div>
                            <small id="voucher-message" class="mt-1 d-block font-weight-bold"></small>
                        </div>

                        <!-- KHU VỰC HIỂN THỊ TIỀN TỆ -->
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Tạm tính</span>
                            <span id="subtotal_text" data-value="{{ $totalAmount ?? 0 }}">{{ number_format($totalAmount ?? 0) }} đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-success" id="discount-row" style="display: none;">
                            <span class="font-weight-bold">Giảm giá (Voucher)</span>
                            <span id="discount_amount_text" class="font-weight-bold">-0 đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Phí vận chuyển</span>
                            <span id="shipping_fee_text">Vui lòng chọn địa chỉ</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                            <span class="font-weight-bold text-dark" style="font-size: 18px;">TỔNG CỘNG:</span>
                            <span class="font-weight-bold" id="total_amount_text" style="color: #e74c3c; font-size: 24px;">{{ number_format($totalAmount ?? 0) }} đ</span>
                        </div>

                        <!-- CÁC THẺ INPUT ẨN GỬI VỀ CONTROLLER -->
                        <input type="hidden" name="applied_voucher" id="applied_voucher_input" value="">
                        <input type="hidden" name="shipping_fee" id="hidden_shipping_fee" value="0">

                        <button type="submit" class="btn btn-orange btn-block rounded-pill py-3 font-weight-bold shadow-sm text-uppercase" style="font-size: 16px; letter-spacing: 0.5px;">
                            <i class="fas fa-check-circle mr-2"></i> Đặt hàng ngay
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Modal Danh Sách Voucher -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold" id="voucherModalLabel"><i class="fas fa-ticket-alt text-orange mr-2"></i>Chọn Mã Giảm Giá</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light pt-2">
                @if(isset($vouchers) && $vouchers->count() > 0)
                    @foreach($vouchers as $vc)
                        <div class="card shadow-sm mb-3 border-0 rounded-lg">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-primary text-white rounded p-2 text-center mr-3" style="min-width: 80px;">
                                    <h6 class="font-weight-bold mb-0" style="font-size: 14px;">{{ $vc->code }}</h6>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">
                                        @if($vc->type == 'percent')
                                            Giảm {{ $vc->discount_value }}% 
                                            <span class="text-muted" style="font-size: 12px;">(Tối đa {{ number_format($vc->max_discount_value) }}đ)</span>
                                        @else
                                            Giảm {{ number_format($vc->discount_value) }}đ
                                        @endif
                                    </h6>
                                    <p class="text-muted mb-0" style="font-size: 12px;">Đơn tối thiểu: {{ number_format($vc->min_order_value) }}đ</p>
                                    <small class="text-danger">
                                        HSD: {{ $vc->end_date ? \Carbon\Carbon::parse($vc->end_date)->format('d/m/Y') : 'Vô thời hạn' }}
                                    </small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-select-voucher font-weight-bold" data-code="{{ $vc->code }}">
                                        Chọn
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                        <p>Hiện tại không có mã giảm giá nào phù hợp.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .custom-input { border: 1px solid #E0E0E0; border-radius: 8px; font-size: 15px; transition: all 0.3s; }
    .custom-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.2rem rgba(211,84,0,0.15); }
    
    .payment-method-card { cursor: pointer; }
    .payment-method-card input[type="radio"] { display: none; }
    .payment-card-content { background: #fff; transition: all 0.3s; }
    .payment-card-content:hover { border-color: #ccc !important; }
    
    .payment-method-card input[type="radio"]:checked + .payment-card-content {
        border-color: var(--primary-color) !important;
        background-color: #FFF6F0;
        box-shadow: 0 4px 10px rgba(211,84,0,0.1) !important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
$(document).ready(function() {
    let subTotal = parseInt($('#subtotal_text').attr('data-value')) || 0;
    let currentShippingFee = 0;
    let currentDiscount = 0;

    function calculateTotal() {
        let finalTotal = subTotal + currentShippingFee - currentDiscount;
        if (finalTotal < 0) finalTotal = 0; 
        $('#total_amount_text').text(new Intl.NumberFormat('vi-VN').format(finalTotal) + ' đ');
    }

    // ==========================================
    // TỰ ĐỘNG TẢI ĐỊA CHỈ TỪ USER_ADDRESSES
    // ==========================================
    let savedDistrict = "{{ old('district_id', $defaultAddress->district_id ?? '') }}";
    let savedWard = "{{ old('ward_code', $defaultAddress->ward_code ?? '') }}";

    function loadDistricts(province_id, selected_district = null) {
        if (province_id) {
            $('#district').html('<option value="">Đang tải...</option>');
            axios.get(`/api/locations/districts/${province_id}`)
                .then((response) => {
                    let html = '<option value="">Chọn Quận/Huyện</option>';
                    let districts = response.data.data || response.data;
                    districts.forEach(element => {
                        let selected = (element.id == selected_district) ? 'selected' : '';
                        html += `<option data-name="${element.name}" value="${element.id}" ${selected}>${element.name}</option>`;
                    });
                    $('#district').html(html);

                    if (selected_district) {
                        loadWards(selected_district, savedWard);
                    } else {
                        $('#ward').html('<option value="">Chọn Phường/Xã</option>');
                    }
                });
        } else {
            $('#district').html('<option value="">Chọn Quận/Huyện</option>');
            $('#ward').html('<option value="">Chọn Phường/Xã</option>');
        }
    }

    function loadWards(district_id, selected_ward = null) {
        if (district_id) {
            $('#ward').html('<option value="">Đang tải...</option>');
            axios.get(`/api/locations/wards/${district_id}`)
                .then((response) => {
                    let html = '<option value="">Chọn Phường/Xã</option>';
                    let wards = response.data.data || response.data;
                    wards.forEach(element => {
                        let selected = (element.code == selected_ward) ? 'selected' : '';
                        html += `<option data-name="${element.name}" value="${element.code}" ${selected}>${element.name}</option>`;
                    });
                    $('#ward').html(html);

                    // Khi nạp xong phường/xã, tự động kích hoạt tính phí ship
                    if (selected_ward) {
                        $('#ward').trigger('change');
                    }
                });
        } else {
            $('#ward').html('<option value="">Chọn Phường/Xã</option>');
        }
    }

    // Sự kiện khi người dùng thay đổi Tỉnh/Thành
    $("#province").on("change", function() {
        savedDistrict = null;
        savedWard = null;
        resetShippingFee(); 
        loadDistricts($(this).val());
    });

    // Sự kiện khi người dùng thay đổi Quận/Huyện
    $("#district").on("change", function() {
        savedWard = null;
        resetShippingFee(); 
        loadWards($(this).val());
    });

    // Tự động load dữ liệu khi vào trang (nếu đã có Tỉnh được chọn sẵn)
    if ($('#province').val()) {
        loadDistricts($('#province').val(), savedDistrict);
    }

    // ==========================================
    // TÍNH PHÍ VẬN CHUYỂN GHN
    // ==========================================
    $("#ward").on("change", function() {
        let wardCode = $(this).val();
        let districtId = $("#district").val();

        if (wardCode && districtId) {
            $('#shipping_fee_text').html('<i class="fas fa-spinner fa-spin"></i> Đang tính phí...');
            
            axios.post('{{ route('payment.calculate_fee') }}', {
                district_id: districtId,
                ward_code: wardCode,
                _token: '{{ csrf_token() }}'
            }).then((response) => {
                if(response.data.success) {
                    updateShippingFee(response.data.fee);
                } else {
                    updateShippingFee(response.data.fee || 30000); 
                }
            }).catch(err => {
                updateShippingFee(30000); 
            });
        }
    });

    function updateShippingFee(fee) {
        currentShippingFee = fee;
        $('#shipping_fee_text').text(new Intl.NumberFormat('vi-VN').format(fee) + ' đ');
        $('#hidden_shipping_fee').val(fee);
        calculateTotal();
    }

    function resetShippingFee() {
        currentShippingFee = 0;
        $('#shipping_fee_text').text('Vui lòng chọn địa chỉ');
        $('#hidden_shipping_fee').val(0);
        calculateTotal();
    }

    // ==========================================
    // LOGIC VOUCHER 
    // ==========================================
    $(document).on('click', '.btn-select-voucher', function(e) {
        e.preventDefault();
        let selectedCode = $(this).attr('data-code'); 
        if (selectedCode) {
            $('#voucher_code').val(selectedCode); 
            $('#voucherModal').modal('hide');     
            $('#btn-apply-voucher').click();      
        }
    });

    $('#btn-apply-voucher').click(function() {
        let code = $('#voucher_code').val().trim();
        let messageEl = $('#voucher-message');

        if(!code) {
            messageEl.text('Vui lòng nhập mã giảm giá!').removeClass('text-success').addClass('text-danger');
            return;
        }

        axios.post('{{ route("checkout.apply_voucher") }}', {
            voucher_code: code,
            total_order: subTotal,
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            if(response.data.success) {
                messageEl.text(response.data.message).removeClass('text-danger').addClass('text-success');
                currentDiscount = response.data.discount_amount;
                
                $('#discount-row').show();
                $('#discount_amount_text').text('-' + new Intl.NumberFormat('vi-VN').format(currentDiscount) + ' đ');
                $('#applied_voucher_input').val(response.data.voucher_code);
                
                calculateTotal();
            } else {
                messageEl.text(response.data.message).removeClass('text-success').addClass('text-danger');
                currentDiscount = 0;
                $('#discount-row').hide();
                $('#applied_voucher_input').val('');
                
                calculateTotal();
            }
        })
        .catch(error => {
            messageEl.text('Lỗi kết nối máy chủ!').removeClass('text-success').addClass('text-danger');
        });
    });

    // ==========================================
    // TẠO CHUỖI ĐỊA CHỈ KHI SUBMIT FORM
    // ==========================================
    $('#checkoutForm').on('submit', function(e) {
        let provinceName = $("#province option:selected").attr('data-name');
        let districtName = $("#district option:selected").attr('data-name');
        let wardName = $("#ward option:selected").attr('data-name');
        let street = $('#street').val();

        let fullAddress = "";
        if (provinceName && districtName && wardName && street) {
            fullAddress = street + ", " + wardName + ", " + districtName + ", " + provinceName;
        }
        
        $('#full_address').val(fullAddress);
    });
});
</script>
@endpush