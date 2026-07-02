@include('User.header')

<section class="breadcrumb-section set-bg" data-setbg="{{ asset('img/breadcrumb.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>Trang Chi Tiết Sản Phẩm</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="product-details spad">
    <div class="container">

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

        <form id="them-vao-gio-hang" action="{{ route('cart.add') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__pic">
                        <div class="product__details__pic__item">
                            <img class="product__details__pic__item--large"
                                src="{{ asset('uploads/products/' . $product->image) }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__text">
                        <h3>{{ $product->name }}</h3>
                        <div class="product__details__rating">
                            <i class="fa fa-star"></i>
                            <span>(18 reviews)</span>
                        </div>
                        <div class="product__details__price">
                            <span id="hien-thi-gia">{{ number_format($product->price) }} VNĐ</span>
                        </div>
                        <p>{{ $product->description }}</p>

                        <div class="product__details__quantity">
                            <div class="quantity">
                                <div class="pro-qty">
                                    <input type="number"
                                           name="quantity"
                                           id="o-so-luong"
                                           value="1"
                                           min="1">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Phiên bản</h5>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($product->variants as $bienThe)
                                    @if($bienThe->price > 0)
                                        <label class="chon-phien-ban">
                                            <input type="radio"
                                                   name="product_variant_id"
                                                   value="{{ $bienThe->id }}"
                                                   data-gia="{{ $bienThe->price }}"
                                                   data-ton-kho="{{ $bienThe->stock }}"
                                                   {{ $bienThe->stock <= 0 ? 'disabled' : '' }}
                                                   required>
                                            <span class="hop-phien-ban">
                                                <strong>{{ $bienThe->edition }}</strong><br>
                                                <small>Giá: {{ number_format($bienThe->price) }} VNĐ</small><br>
                                                <small class="{{ $bienThe->stock > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $bienThe->stock > 0 ? 'Còn ' . $bienThe->stock : 'Hết hàng' }}
                                                </small>
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="action_type" value="add_to_cart" class="primary-btn" style="border:none;">
                                Thêm vào giỏ hàng
                            </button>

                            <button type="submit" name="action_type" value="buy_now" class="primary-btn" style="border:none; background:#e74c3c;">
                                Mua ngay
                            </button>
                            <a href="#" class="heart-icon"><span class="icon_heart_alt"></span></a>
                        </div>    
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
    .chon-phien-ban input { display:none; }
    .hop-phien-ban { display:block; min-width:140px; padding:12px; border:1px solid #ddd; border-radius:8px; cursor:pointer; text-align:center; }
    .chon-phien-ban input:checked + .hop-phien-ban { border:2px solid #7fad39; background:#f6fff0; }
    .chon-phien-ban input:disabled + .hop-phien-ban { opacity:0.5; cursor:not-allowed; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const danhSachRadio = document.querySelectorAll('input[name="product_variant_id"]');
    const oHienThiGia = document.getElementById('hien-thi-gia');
    const oSoLuong = document.getElementById('o-so-luong');
    const oForm = document.getElementById('them-vao-gio-hang');

    let tonKhoHienTai = 0;

    // Khi chọn biến thể → cập nhật giá và lưu tồn kho
    danhSachRadio.forEach(function (oRadio) {
        oRadio.addEventListener('change', function () {
            const gia = parseInt(this.dataset.gia);
            tonKhoHienTai = parseInt(this.dataset.tonKho);

            oHienThiGia.innerHTML = gia.toLocaleString('vi-VN') + ' VNĐ';
            oSoLuong.value = 1;
        });
    });

    // Khi nhập số lượng → tự điều chỉnh không hiện popup browser
    oSoLuong.addEventListener('input', function () {
        if (parseInt(this.value) < 1 || isNaN(parseInt(this.value))) this.value = 1;
        if (tonKhoHienTai > 0 && parseInt(this.value) > tonKhoHienTai) this.value = tonKhoHienTai;
    });

    // Khi submit → kiểm tra rồi mới cho submit
    oForm.addEventListener('submit', function (e) {
        const bienTheDangChon = document.querySelector('input[name="product_variant_id"]:checked');

        if (!bienTheDangChon) {
            e.preventDefault();
            hienThongBao('Vui lòng chọn phiên bản!', 'warning');
            return;
        }

        const soLuong = parseInt(oSoLuong.value);
        if (soLuong > tonKhoHienTai) {
            e.preventDefault();
            hienThongBao('Số lượng không được vượt quá tồn kho (' + tonKhoHienTai + ')!', 'error');
            return;
        }

        // Hợp lệ → submit bình thường
    });

    function hienThongBao(noiDung, loai) {
        // Xóa thông báo cũ nếu có
        const cuThongBao = document.getElementById('thong-bao-dong');
        if (cuThongBao) cuThongBao.remove();

        const mauNen = loai === 'error' ? '#f8d7da' : '#fff3cd';
        const mauVien = loai === 'error' ? '#dc3545' : '#ffc107';

        const oThongBao = document.createElement('div');
        oThongBao.id = 'thong-bao-dong';
        oThongBao.style.cssText = `
            margin: 15px 0;
            padding: 12px 20px;
            background: ${mauNen};
            border: 1px solid ${mauVien};
            border-radius: 6px;
        `;
        oThongBao.textContent = noiDung;

        // Chèn vào trước form
        oForm.parentNode.insertBefore(oThongBao, oForm);

        // Tự xóa sau 4 giây
        setTimeout(function () { oThongBao.remove(); }, 4000);

        // Scroll lên để thấy thông báo
        oThongBao.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

@include('User.footer')