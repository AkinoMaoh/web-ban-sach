@include('User.header')

<section class="shoping-cart spad">
    <div class="container">

        @if(session('warning'))
            <div class="alert alert-warning" style="margin:15px 0; padding:12px 20px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px;">
                {!! session('warning') !!}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin:15px 0; padding:12px 20px; background:#f8d7da; border:1px solid #dc3545; border-radius:6px;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="margin:15px 0; padding:12px 20px; background:#d4edda; border:1px solid #28a745; border-radius:6px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('cart.update') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="shoping__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Chọn</th>
                                    <th class="shoping__product">Sản phẩm</th>
                                    <th>Biến thể</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $tongTien = 0; @endphp
                                @foreach($danhSachGioHang as $sanPham)
                                @php
                                    $bienThe = $sanPham->productVariant;
                                    $sach = $bienThe->product;
                                    $tatCaBienThe = $sach->variants;
                                    $thanhTien = $bienThe->price * $sanPham->quantity;
                                    $tongTien += $thanhTien;
                                @endphp
                                <tr data-ma-san-pham="{{ $sanPham->id }}">
                                    <td>
                                        <input type="checkbox" name="selected[]" value="{{ $sanPham->id }}" checked>
                                    </td>
                                    <td class="shoping__cart__item">
                                        <img src="{{ asset('uploads/products/' . $sach->image) }}" width="70" alt="">
                                        <h5>{{ $sach->name }}</h5>
                                    </td>
                                    <td>
                                        <select name="variants[{{ $sanPham->id }}]"
                                                class="chon-bien-the"
                                                data-ma-san-pham="{{ $sanPham->id }}"
                                                style="min-width:150px; padding:6px; border:1px solid #ddd; border-radius:6px;">
                                            @foreach($tatCaBienThe as $bt)
                                                @if($bt->stock > 0 || $bt->id == $bienThe->id)
                                                <option value="{{ $bt->id }}"
                                                        data-gia="{{ $bt->price }}"
                                                        data-ton-kho="{{ $bt->stock }}"
                                                        {{ $bt->id == $bienThe->id ? 'selected' : '' }}>
                                                    {{ $bt->edition }} - {{ number_format($bt->price, 0, ',', '.') }}đ
                                                </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="shoping__cart__price o-gia" id="gia-{{ $sanPham->id }}">
                                        {{ number_format($bienThe->price, 0, ',', '.') }} đ
                                    </td>
                                    <td class="shoping__cart__quantity">
                                        <div class="quantity">
                                            <div class="pro-qty">
                                                <input type="number"
                                                       name="quantities[{{ $sanPham->id }}]"
                                                       value="{{ $sanPham->quantity }}"
                                                       min="1"
                                                       max="{{ $bienThe->stock }}"
                                                       class="o-so-luong"
                                                       data-ma-san-pham="{{ $sanPham->id }}"
                                                       style="width:60px; text-align:center;">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="shoping__cart__total o-thanh-tien" id="thanh-tien-{{ $sanPham->id }}">
                                        {{ number_format($thanhTien, 0, ',', '.') }} đ
                                    </td>
                                    <td class="shoping__cart__item__close">
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('xoa-{{ $sanPham->id }}').submit();">
                                            <span class="icon_close"></span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="shoping__cart__btns">
                        <button type="submit" class="primary-btn cart-btn">CẬP NHẬT GIỎ HÀNG</button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="shoping__checkout">
                        <h5>Tổng đơn hàng</h5>
                        <ul>
                            <li>Tổng thanh toán <span id="tong-tien">{{ number_format($tongTien, 0, ',', '.') }} đ</span></li>
                        </ul>
                        <a href="javascript:void(0)" onclick="xacNhanThanhToan()" class="primary-btn">TIẾN HÀNH THANH TOÁN</a>
                    </div>
                </div>
            </div>
        </form>

        @foreach($danhSachGioHang as $sanPham)
            <form id="xoa-{{ $sanPham->id }}" action="{{ route('cart.remove', $sanPham->id) }}" method="POST" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endforeach

    </div>
</section>

<script>
// Khi đổi biến thể → cập nhật giá, tồn kho max, thành tiền
document.querySelectorAll('.chon-bien-the').forEach(function(oBienThe) {
    oBienThe.addEventListener('change', function() {
        const maSanPham = this.dataset.maSanPham;
        const tuyChnDangChon = this.options[this.selectedIndex];
        const giaMoi = parseInt(tuyChnDangChon.dataset.gia);
        const tonKhoMoi = parseInt(tuyChnDangChon.dataset.tonKho);
        const oSoLuong = document.querySelector(`.o-so-luong[data-ma-san-pham="${maSanPham}"]`);

        // Cập nhật max theo tồn kho biến thể mới
        oSoLuong.max = tonKhoMoi;
        if (parseInt(oSoLuong.value) > tonKhoMoi) {
            oSoLuong.value = tonKhoMoi;
        }

        const soLuong = parseInt(oSoLuong.value) || 1;
        document.getElementById('gia-' + maSanPham).textContent = dinhDangTien(giaMoi) + ' đ';
        document.getElementById('thanh-tien-' + maSanPham).textContent = dinhDangTien(giaMoi * soLuong) + ' đ';

        tinhLaiTongTien();
    });
});

// Khi đổi số lượng → cập nhật thành tiền và tổng
document.querySelectorAll('.o-so-luong').forEach(function(oSoLuong) {
    oSoLuong.addEventListener('input', function() {
        const maSanPham = this.dataset.maSanPham;
        const tonKhoToiDa = parseInt(this.max);

        if (parseInt(this.value) > tonKhoToiDa) {
            this.value = tonKhoToiDa;
            alert('Số lượng không được vượt quá tồn kho (' + tonKhoToiDa + ')!');
        }
        if (parseInt(this.value) < 1) this.value = 1;

        const soLuong = parseInt(this.value) || 1;
        const oBienThe = document.querySelector(`.chon-bien-the[data-ma-san-pham="${maSanPham}"]`);
        const gia = parseInt(oBienThe.options[oBienThe.selectedIndex].dataset.gia);

        document.getElementById('thanh-tien-' + maSanPham).textContent = dinhDangTien(gia * soLuong) + ' đ';
        tinhLaiTongTien();
    });
});

function tinhLaiTongTien() {
    let tongTien = 0;
    document.querySelectorAll('.chon-bien-the').forEach(function(oBienThe) {
        const maSanPham = oBienThe.dataset.maSanPham;
        const gia = parseInt(oBienThe.options[oBienThe.selectedIndex].dataset.gia);
        const soLuong = parseInt(document.querySelector(`.o-so-luong[data-ma-san-pham="${maSanPham}"]`).value) || 1;
        const daDuocChon = document.querySelector(`input[name="selected[]"][value="${maSanPham}"]`).checked;
        if (daDuocChon) tongTien += gia * soLuong;
    });
    document.getElementById('tong-tien').textContent = dinhDangTien(tongTien) + ' đ';
}

// Khi tick/untick checkbox → cập nhật tổng
document.querySelectorAll('input[name="selected[]"]').forEach(function(oCheckbox) {
    oCheckbox.addEventListener('change', tinhLaiTongTien);
});

function dinhDangTien(soTien) {
    return soTien.toLocaleString('vi-VN');
}

function xacNhanThanhToan() {
    let danhSachDaChon = [];
    document.querySelectorAll('input[name="selected[]"]:checked').forEach((oCheckbox) => {
        danhSachDaChon.push(oCheckbox.value);
    });

    if (danhSachDaChon.length === 0) {
        alert("Vui lòng chọn ít nhất một sản phẩm!");
        return;
    }

    window.location.href = "{{ route('checkout.index') }}?items=" + danhSachDaChon.join(',');
}
</script>

@include('User.footer')