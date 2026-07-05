@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Giỏ hàng của bạn</li>
            </ol>
        </nav>
    </div>
</div>

<section class="shoping-cart spad mb-5 pb-5">
    <div class="container">

        <!-- Cảnh báo Flash Messages -->
        @if(session('warning'))
            <div class="alert alert-warning shadow-sm border-0 mb-4" style="border-left: 5px solid #ffc107; border-radius: 6px;">
                <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('warning') !!}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-left: 5px solid #dc3545; border-radius: 6px;">
                <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 mb-4" style="border-left: 5px solid #28a745; border-radius: 6px;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(count($danhSachGioHang) == 0)
            <!-- Giỏ hàng trống -->
            <div class="text-center py-5 bg-white shadow-sm rounded border">
                <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" alt="Empty Cart" style="width: 120px; opacity: 0.5; margin-bottom: 20px;">
                <h4 class="serif-font text-muted mb-3">Giỏ hàng của bạn đang trống</h4>
                <a href="{{ route('user.shop') }}" class="btn btn-orange rounded-pill px-4">Tiếp tục mua sách</a>
            </div>
        @else
            <!-- Có sản phẩm -->
            <form action="{{ route('cart.update') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Cột danh sách sản phẩm -->
                    <div class="col-lg-8 mb-4">
                        <div class="bg-white rounded shadow-sm border overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0 align-middle text-center" style="min-width: 700px;">
                                    <thead class="bg-light border-bottom text-muted" style="font-size: 14px; text-transform: uppercase;">
                                        <tr>
                                            <th style="width: 50px;">Chọn</th>
                                            <th class="text-left" style="width: 280px;">Sản phẩm</th>
                                            <th>Phiên bản</th>
                                            <th>Đơn giá</th>
                                            <th style="width: 120px;">Số lượng</th>
                                            <th>Thành tiền</th>
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
                                            <tr class="border-bottom" data-ma-san-pham="{{ $sanPham->id }}">
                                                
                                                <!-- Checkbox -->
                                                <td class="align-middle">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input checkbox-cart" id="check-{{ $sanPham->id }}" name="selected[]" value="{{ $sanPham->id }}" checked>
                                                        <label class="custom-control-label" for="check-{{ $sanPham->id }}"></label>
                                                    </div>
                                                </td>

                                                <!-- Ảnh & Tên Sách -->
                                                <td class="text-left align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ route('user.productDetails', $sach->id) }}">
                                                            <img src="{{ asset('uploads/products/' . $sach->image) }}" class="rounded shadow-sm" style="width: 60px; height: 85px; object-fit: cover;" alt="{{ $sach->name }}">
                                                        </a>
                                                        <div class="ml-3">
                                                            <a href="{{ route('user.productDetails', $sach->id) }}" class="text-dark font-weight-bold text-decoration-none" style="font-size: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                                {{ $sach->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Chọn phiên bản -->
                                                <td class="align-middle">
                                                    <select name="variants[{{ $sanPham->id }}]"
                                                            class="custom-select custom-select-sm chon-bien-the shadow-none"
                                                            data-ma-san-pham="{{ $sanPham->id }}"
                                                            style="border-radius: 6px; font-size: 13px;">
                                                        @foreach($tatCaBienThe as $bt)
                                                            @if($bt->stock > 0 || $bt->id == $bienThe->id)
                                                                <option value="{{ $bt->id }}"
                                                                        data-gia="{{ $bt->price }}"
                                                                        data-ton-kho="{{ $bt->stock }}"
                                                                        {{ $bt->id == $bienThe->id ? 'selected' : '' }}>
                                                                    {{ $bt->edition }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <!-- Đơn giá -->
                                                <td class="align-middle font-weight-bold text-muted o-gia" id="gia-{{ $sanPham->id }}" style="font-size: 14px;">
                                                    {{ number_format($bienThe->price, 0, ',', '.') }} đ
                                                </td>

                                                <!-- Số lượng -->
                                                <td class="align-middle">
                                                    <input type="number"
                                                           name="quantities[{{ $sanPham->id }}]"
                                                           value="{{ $sanPham->quantity }}"
                                                           min="1"
                                                           max="{{ $bienThe->stock }}"
                                                           class="form-control form-control-sm text-center o-so-luong shadow-none mx-auto"
                                                           data-ma-san-pham="{{ $sanPham->id }}"
                                                           style="width: 70px; border-radius: 6px;">
                                                </td>

                                                <!-- Thành tiền -->
                                                <td class="align-middle font-weight-bold o-thanh-tien" id="thanh-tien-{{ $sanPham->id }}" style="color: var(--primary-color); font-size: 15px;">
                                                    {{ number_format($thanhTien, 0, ',', '.') }} đ
                                                </td>

                                                <!-- Nút xóa -->
                                                <td class="align-middle">
                                                    <a href="#" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" onclick="event.preventDefault(); document.getElementById('xoa-{{ $sanPham->id }}').submit();" title="Xóa">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Nút cập nhật giỏ hàng -->
                            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                                <a href="{{ route('user.shop') }}" class="btn btn-outline-dark btn-sm rounded-pill font-weight-bold px-3"><i class="fas fa-arrow-left mr-1"></i> Mua thêm</a>
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill font-weight-bold px-4 shadow-sm"><i class="fas fa-sync-alt mr-1"></i> Cập nhật giỏ hàng</button>
                            </div>
                        </div>
                    </div>

                    <!-- Cột Tổng tiền & Thanh toán -->
                    <div class="col-lg-4">
                        <div class="bg-white p-4 rounded shadow-sm border sticky-top" style="top: 100px;">
                            <h4 class="serif-font font-weight-bold border-bottom pb-3 mb-4">Tóm tắt đơn hàng</h4>
                            
                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>Tạm tính:</span>
                                <span id="tong-tien-tam" class="font-weight-bold">{{ number_format($tongTien, 0, ',', '.') }} đ</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>Phí vận chuyển:</span>
                                <span><i>Tính ở bước sau</i></span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="font-weight-bold text-dark">Tổng cộng:</span>
                                <span id="tong-tien" class="font-weight-bold" style="color: var(--primary-color); font-size: 22px;">{{ number_format($tongTien, 0, ',', '.') }} đ</span>
                            </div>

                            <button type="button" onclick="xacNhanThanhToan()" class="btn btn-orange btn-block rounded-pill py-2 font-weight-bold shadow-sm" style="font-size: 16px;">
                                TIẾN HÀNH THANH TOÁN
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Form ẩn để xóa sản phẩm -->
            @foreach($danhSachGioHang as $sanPham)
                <form id="xoa-{{ $sanPham->id }}" action="{{ route('cart.remove', $sanPham->id) }}" method="POST" style="display:none;">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            
        @endif
    </div>
</section>
@endsection

@push('scripts')
<style>
    /* Làm đẹp thẻ Checkbox */
    .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .custom-control-input:focus ~ .custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(211, 84, 0, 0.25);
    }
</style>

<script>
// Giữ nguyên 100% logic JavaScript cũ của bạn
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

document.querySelectorAll('.o-so-luong').forEach(function(oSoLuong) {
    oSoLuong.addEventListener('input', function() {
        const maSanPham = this.dataset.maSanPham;
        const tonKhoToiDa = parseInt(this.max);

        if (parseInt(this.value) > tonKhoToiDa) {
            this.value = tonKhoToiDa;
            alert('Số lượng không được vượt quá số sách còn trong kho (' + tonKhoToiDa + ')!');
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
    
    // Cập nhật cả 2 chỗ (tạm tính và tổng cộng)
    document.getElementById('tong-tien-tam').textContent = dinhDangTien(tongTien) + ' đ';
    document.getElementById('tong-tien').textContent = dinhDangTien(tongTien) + ' đ';
}

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
        alert("Vui lòng chọn ít nhất một cuốn sách để thanh toán!");
        return;
    }

    // Gửi id qua URL đúng như logic ban đầu
    window.location.href = "{{ route('checkout.index') }}?items=" + danhSachDaChon.join(',');
}
</script>
@endpush