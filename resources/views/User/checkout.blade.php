@include('User.header')

<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <h2 style="text-align: center; margin-bottom: 30px;">Tiến hành Thanh Toán</h2>
    
    {{-- Hiển thị thông báo lỗi từ Controller --}}
    @if(session('error'))
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 25px; border-radius: 4px; border: 1px solid #f5c6cb;">
            <strong>Lỗi thanh toán:</strong> {{ session('error') }}
        </div>
    @endif

    {{-- Hiển thị lỗi Validate (nhập thiếu) --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 25px; border-radius: 4px; border: 1px solid #f5c6cb;">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>
            <ul style="margin-top: 5px; margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" style="display: flex; gap: 40px;">
        @csrf

        <div style="flex: 2;">
            <h3>1. Thông tin người nhận</h3>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Họ và tên *</label>
                <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Email *</label>
                <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email ?? '') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Số điện thoại *</label>
                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Tỉnh / Thành phố *</label>
                    <select id="province" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">Chọn Tỉnh/Thành</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Quận / Huyện *</label>
                    <select id="district" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">Chọn Quận/Huyện</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Phường / Xã *</label>
                    <select id="ward" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">Chọn Phường/Xã</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Số nhà, Tên đường *</label>
                <input type="text" id="street" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <input type="hidden" name="full_address" id="full_address">

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Ghi chú đơn hàng</label>
                <textarea name="order_notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">{{ old('order_notes') }}</textarea>
            </div>

            <h3 style="margin-top: 30px;">2. Phương thức thanh toán</h3>
            <div style="padding: 15px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 10px;">
                <input type="radio" id="cod" name="payment_method" value="cod" checked>
                <label for="cod" style="font-weight: bold; cursor: pointer;">Thanh toán khi nhận hàng (COD)</label>
            </div>
            
            <div style="padding: 15px; border: 1px solid #ccc; border-radius: 5px;">
                <input type="radio" id="vnpay" name="payment_method" value="vnpay">
                <label for="vnpay" style="font-weight: bold; cursor: pointer;">Thanh toán qua VNPAY</label>
            </div>
        </div>

        <div style="flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; height: fit-content;">
            <h3>Đơn hàng của bạn</h3>
            <hr style="margin: 15px 0;">
            
            @if(!empty($cart))
                @foreach($cart as $id => $details)
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>{{ $details['name'] ?? 'Sách ID '.$id }} x{{ $details['quantity'] }}</span>
                    <span>{{ number_format($details['price'] * $details['quantity']) }}đ</span>
                </div>
                @endforeach
            @else
                <div style="margin-bottom: 10px; color: #777;">Giỏ hàng của bạn đang trống.</div>
            @endif

            <hr style="margin: 15px 0;">
            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; margin-bottom: 20px;">
                <span>Tổng tiền:</span>
                <span style="color: red;">{{ number_format($totalAmount ?? 0) }}đ</span>
            </div>
            <button type="submit" style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer;">
                ĐẶT HÀNG NGAY
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
$(document).ready(function() {
    $('select').niceSelect();
    const host = "https://provinces.open-api.vn/api/";
    
    var callAPI = (api) => {
        return axios.get(api).then((response) => {
            renderData(response.data, "province");
        });
    }
    callAPI('https://provinces.open-api.vn/api/?depth=1');

    var callApiDistrict = (api) => {
        return axios.get(api).then((response) => {
            renderData(response.data.districts, "district");
        });
    }

    var callApiWard = (api) => {
        return axios.get(api).then((response) => {
            renderData(response.data.wards, "ward");
        });
    }

    var renderData = (array, select) => {
        let row = '<option disable value="">Chọn</option>';
        array.forEach(element => {
            row += `<option data-name="${element.name}" value="${element.code}">${element.name}</option>`
        });
        
        $("#" + select).html(row);
        $("#" + select).niceSelect('update');
    }

    $("#province").on("change", function() {
        callApiDistrict(host + "p/" + $(this).val() + "?depth=2");
        $("#ward").html('<option value="">Chọn Phường/Xã</option>');
        $("#ward").niceSelect('update');
    });

    $("#district").on("change", function() {
        callApiWard(host + "d/" + $(this).val() + "?depth=2");
    });

    // Thay đổi logic Javascript để đẩy quyền báo lỗi cho Laravel xử lý
    $('#checkoutForm').on('submit', function(e) {
        let provinceName = $("#province option:selected").attr('data-name');
        let districtName = $("#district option:selected").attr('data-name');
        let wardName = $("#ward option:selected").attr('data-name');
        let street = $('#street').val();

        let fullAddress = "";
        if (provinceName && districtName && wardName && street) {
            fullAddress = street + ", " + wardName + ", " + districtName + ", " + provinceName;
        }
        
        // Gán vào input ẩn. Nếu thiếu dữ liệu, fullAddress sẽ rỗng, Laravel sẽ tự báo lỗi đỏ!
        $('#full_address').val(fullAddress);
    });
});
</script>

@include('User.footer')