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
        <di@extends('layout.user')

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
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" class="form-control form-control-lg custom-input" placeholder="Nhập tên người nhận">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Email <span class="text-danger">*</span></label>
                                <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email ?? '') }}" class="form-control form-control-lg custom-input" placeholder="ten@gmail.com">
                            </div>
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" class="form-control form-control-lg custom-input" placeholder="098...">
                            </div>
                        </div>

                        <h5 class="serif-font font-weight-bold mb-3 mt-2">Địa chỉ chi tiết</h5>
                        
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label class="text-muted small font-weight-bold text-uppercase">Tỉnh / Thành <span class="text-danger">*</span></label>
                                <select id="province" class="custom-select custom-select-lg custom-input">
                                    <option value="">Chọn Tỉnh/Thành</option>
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
                            <input type="text" id="street" class="form-control form-control-lg custom-input" placeholder="Ví dụ: 123 Đường Lê Lợi...">
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
                                            
                                            <!-- Hiển thị gạch ngang giá gốc nếu có giảm giá -->
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

                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Tạm tính</span>
                            <span id="subtotal_text" data-value="{{ $totalAmount ?? 0 }}">{{ number_format($totalAmount ?? 0) }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Phí vận chuyển</span>
                            <span id="shipping_fee_text">Vui lòng chọn địa chỉ</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                            <span class="font-weight-bold text-dark" style="font-size: 18px;">TỔNG CỘNG:</span>
                            <span class="font-weight-bold" id="total_amount_text" style="color: #e74c3c; font-size: 24px;">{{ number_format($totalAmount ?? 0) }} đ</span>
                        </div>

                        <!-- Thêm dòng này để gửi phí ship về Controller -->
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

@endsection

@push('scripts')
<style>
    /* Làm đẹp Input */
    .custom-input { border: 1px solid #E0E0E0; border-radius: 8px; font-size: 15px; transition: all 0.3s; }
    .custom-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.2rem rgba(211,84,0,0.15); }
    
    /* Làm đẹp Card Phương thức thanh toán */
    .payment-method-card { cursor: pointer; }
    .payment-method-card input[type="radio"] { display: none; }
    .payment-card-content { background: #fff; transition: all 0.3s; }
    .payment-card-content:hover { border-color: #ccc !important; }
    
    /* Hiệu ứng khi được chọn */
    .payment-method-card input[type="radio"]:checked + .payment-card-content {
        border-color: var(--primary-color) !important;
        background-color: #FFF6F0;
        box-shadow: 0 4px 10px rgba(211,84,0,0.1) !important;
    }
</style>

<!-- Sử dụng axios để gọi API -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Gọi API NỘI BỘ lấy Tỉnh/Thành từ Database
    axios.get('/api/locations/provinces')
        .then((response) => {
            let html = '<option value="">Chọn Tỉnh/Thành</option>';
            let provinces = response.data.data || response.data; 
            provinces.forEach(element => {
                html += `<option data-name="${element.name}" value="${element.id}">${element.name}</option>`;
            });
            $("#province").html(html);
        }).catch(err => console.log('Lỗi tải Tỉnh/Thành:', err));

    // 2. Khi đổi Tỉnh -> Load Huyện từ API NỘI BỘ
    $("#province").on("change", function() {
        let provinceId = $(this).val();
        resetShippingFee(); 

        // [QUAN TRỌNG] Làm trống ngay lập tức Quận/Huyện và Phường/Xã cũ
        $("#district").html('<option value="">Chọn Quận/Huyện</option>');
        $("#ward").html('<option value="">Chọn Phường/Xã</option>');

        if(provinceId) {
            axios.get(`/api/locations/districts/${provinceId}`)
                .then((response) => {
                    let html = '<option value="">Chọn Quận/Huyện</option>';
                    let districts = response.data.data || response.data;
                    districts.forEach(element => {
                        html += `<option data-name="${element.name}" value="${element.id}">${element.name}</option>`;
                    });
                    $("#district").html(html);
                });
        }
    });

    // 3. Khi đổi Huyện -> Load Xã từ API NỘI BỘ
    $("#district").on("change", function() {
        let districtId = $(this).val();
        resetShippingFee(); 

        // [QUAN TRỌNG] Làm trống Phường/Xã cũ ngay lập tức
        $("#ward").html('<option value="">Chọn Phường/Xã</option>');

        if(districtId) {
            axios.get(`/api/locations/wards/${districtId}`)
                .then((response) => {
                    let html = '<option value="">Chọn Phường/Xã</option>';
                    let wards = response.data.data || response.data;
                    wards.forEach(element => {
                        html += `<option data-name="${element.name}" value="${element.code}">${element.name}</option>`;
                    });
                    $("#ward").html(html);
                });
        }
    });

    // 4. Khi chọn xong Phường/Xã -> Gọi Backend Laravel để tính phí ship GHN
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
                    let fee = response.data.fee;
                    updateShippingFee(fee);
                } else {
                    alert('Không thể tính phí vận chuyển lúc này, áp dụng phí mặc định 30,000đ.');
                    updateShippingFee(response.data.fee); 
                }
            }).catch(err => {
                updateShippingFee(30000); 
            });
        }
    });

    // Hàm cập nhật tiền ship ra màn hình
    function updateShippingFee(fee) {
        let subTotal = parseInt($('#subtotal_text').attr('data-value')) || 0;
        
        $('#shipping_fee_text').text(new Intl.NumberFormat('vi-VN').format(fee) + ' đ');
        $('#total_amount_text').text(new Intl.NumberFormat('vi-VN').format(subTotal + fee) + ' đ');
        $('#hidden_shipping_fee').val(fee);
    }

    // Hàm reset tiền ship
    function resetShippingFee() {
        let subTotal = parseInt($('#subtotal_text').attr('data-value')) || 0;
        
        $('#shipping_fee_text').text('Vui lòng chọn địa chỉ');
        $('#total_amount_text').text(new Intl.NumberFormat('vi-VN').format(subTotal) + ' đ');
        $('#hidden_shipping_fee').val(0);
    }

    // 5. Khi Submit form -> Nối chuỗi full địa chỉ gửi cho Laravel Controller
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
@endpushv class="text-center mb-5">
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
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" class="form-control form-control-lg custom-input" placeholder="Nhập tên người nhận">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Email <span class="text-danger">*</span></label>
                                <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email ?? '') }}" class="form-control form-control-lg custom-input" placeholder="ten@gmail.com">
                            </div>
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" class="form-control form-control-lg custom-input" placeholder="098...">
                            </div>
                        </div>

                        <h5 class="serif-font font-weight-bold mb-3 mt-2">Địa chỉ chi tiết</h5>
                        
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label class="text-muted small font-weight-bold text-uppercase">Tỉnh / Thành <span class="text-danger">*</span></label>
                                <select id="province" class="custom-select custom-select-lg custom-input">
                                    <option value="">Chọn Tỉnh/Thành</option>
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
                            <input type="text" id="street" class="form-control form-control-lg custom-input" placeholder="Ví dụ: 123 Đường Lê Lợi...">
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
                                            
                                            <!-- Hiển thị gạch ngang giá gốc nếu có giảm giá -->
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

                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Tạm tính</span>
                            <span id="subtotal_text" data-value="{{ $totalAmount ?? 0 }}">{{ number_format($totalAmount ?? 0) }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Phí vận chuyển</span>
                            <span id="shipping_fee_text">Vui lòng chọn địa chỉ</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                            <span class="font-weight-bold text-dark" style="font-size: 18px;">TỔNG CỘNG:</span>
                            <span class="font-weight-bold" id="total_amount_text" style="color: #e74c3c; font-size: 24px;">{{ number_format($totalAmount ?? 0) }} đ</span>
                        </div>

                        <!-- Thêm dòng này để gửi phí ship về Controller -->
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

@endsection

@push('scripts')
<style>
    /* Làm đẹp Input */
    .custom-input { border: 1px solid #E0E0E0; border-radius: 8px; font-size: 15px; transition: all 0.3s; }
    .custom-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.2rem rgba(211,84,0,0.15); }
    
    /* Làm đẹp Card Phương thức thanh toán */
    .payment-method-card { cursor: pointer; }
    .payment-method-card input[type="radio"] { display: none; }
    .payment-card-content { background: #fff; transition: all 0.3s; }
    .payment-card-content:hover { border-color: #ccc !important; }
    
    /* Hiệu ứng khi được chọn */
    .payment-method-card input[type="radio"]:checked + .payment-card-content {
        border-color: var(--primary-color) !important;
        background-color: #FFF6F0;
        box-shadow: 0 4px 10px rgba(211,84,0,0.1) !important;
    }
</style>

<!-- Sử dụng axios để gọi API -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Gọi API NỘI BỘ lấy Tỉnh/Thành từ Database
    axios.get('/api/locations/provinces')
        .then((response) => {
            let html = '<option value="">Chọn Tỉnh/Thành</option>';
            let provinces = response.data.data || response.data; 
            provinces.forEach(element => {
                html += `<option data-name="${element.name}" value="${element.id}">${element.name}</option>`;
            });
            $("#province").html(html);
        }).catch(err => console.log('Lỗi tải Tỉnh/Thành:', err));

    // 2. Khi đổi Tỉnh -> Load Huyện từ API NỘI BỘ
    $("#province").on("change", function() {
        let provinceId = $(this).val();
        resetShippingFee(); 

        // [QUAN TRỌNG] Làm trống ngay lập tức Quận/Huyện và Phường/Xã cũ
        $("#district").html('<option value="">Chọn Quận/Huyện</option>');
        $("#ward").html('<option value="">Chọn Phường/Xã</option>');

        if(provinceId) {
            axios.get(`/api/locations/districts/${provinceId}`)
                .then((response) => {
                    let html = '<option value="">Chọn Quận/Huyện</option>';
                    let districts = response.data.data || response.data;
                    districts.forEach(element => {
                        html += `<option data-name="${element.name}" value="${element.id}">${element.name}</option>`;
                    });
                    $("#district").html(html);
                });
        }
    });

    // 3. Khi đổi Huyện -> Load Xã từ API NỘI BỘ
    $("#district").on("change", function() {
        let districtId = $(this).val();
        resetShippingFee(); 

        // [QUAN TRỌNG] Làm trống Phường/Xã cũ ngay lập tức
        $("#ward").html('<option value="">Chọn Phường/Xã</option>');

        if(districtId) {
            axios.get(`/api/locations/wards/${districtId}`)
                .then((response) => {
                    let html = '<option value="">Chọn Phường/Xã</option>';
                    let wards = response.data.data || response.data;
                    wards.forEach(element => {
                        html += `<option data-name="${element.name}" value="${element.code}">${element.name}</option>`;
                    });
                    $("#ward").html(html);
                });
        }
    });

    // 4. Khi chọn xong Phường/Xã -> Gọi Backend Laravel để tính phí ship GHN
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
                    let fee = response.data.fee;
                    updateShippingFee(fee);
                } else {
                    alert('Không thể tính phí vận chuyển lúc này, áp dụng phí mặc định 30,000đ.');
                    updateShippingFee(response.data.fee); 
                }
            }).catch(err => {
                updateShippingFee(30000); 
            });
        }
    });

    // Hàm cập nhật tiền ship ra màn hình
    function updateShippingFee(fee) {
        let subTotal = parseInt($('#subtotal_text').attr('data-value')) || 0;
        
        $('#shipping_fee_text').text(new Intl.NumberFormat('vi-VN').format(fee) + ' đ');
        $('#total_amount_text').text(new Intl.NumberFormat('vi-VN').format(subTotal + fee) + ' đ');
        $('#hidden_shipping_fee').val(fee);
    }

    // Hàm reset tiền ship
    function resetShippingFee() {
        let subTotal = parseInt($('#subtotal_text').attr('data-value')) || 0;
        
        $('#shipping_fee_text').text('Vui lòng chọn địa chỉ');
        $('#total_amount_text').text(new Intl.NumberFormat('vi-VN').format(subTotal) + ' đ');
        $('#hidden_shipping_fee').val(0);
    }

    // 5. Khi Submit form -> Nối chuỗi full địa chỉ gửi cho Laravel Controller
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