@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.shop') }}" class="text-muted">Tủ sách</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="product-details spad mb-5 pb-5">
    <div class="container">
        <!-- Thông báo -->
        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0" style="border-left: 5px solid #dc3545; border-radius: 6px;"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0" style="border-left: 5px solid #28a745; border-radius: 6px;"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</div>
        @endif

        <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
            <form id="them-vao-gio-hang" action="{{ route('cart.add') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Ảnh Sách -->
                    <div class="col-lg-5 mb-4 mb-lg-0 text-center">
                        <img src="{{ asset('uploads/products/' . $product->image) }}" class="img-fluid rounded shadow" alt="{{ $product->name }}" style="max-height: 500px; object-fit: contain;">
                    </div>
                    
                    <!-- Thông tin Sách -->
                    <div class="col-lg-7 pl-lg-5">
                        <h1 class="serif-font font-weight-bold mb-3" style="color: var(--text-main); line-height: 1.3;">{{ $product->name }}</h1>
                        
                        <h2 class="display-4 font-weight-bold mb-4" style="color: var(--primary-color);">
                            <span id="hien-thi-gia">{{ number_format($product->price) }} VNĐ</span>
                        </h2>
                        
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">{{ $product->description }}</p>

                        <!-- Box Chọn Phiên Bản -->
                        <div class="mt-4 pt-3 border-top">
    <h5 class="serif-font font-weight-bold mb-3">Chọn phiên bản:</h5>
    <div class="d-flex flex-wrap gap-2 mb-4">
        @php $hasChecked = false; @endphp
        @foreach($product->variants as $bienThe)
            @if($bienThe->price > 0)
                <label class="chon-phien-ban mb-2 mr-2">
                    <input type="radio" 
                           name="product_variant_id" 
                           value="{{ $bienThe->id }}" 
                           data-gia="{{ $bienThe->price }}" 
                           data-ton-kho="{{ $bienThe->stock }}" 
                           {{ $bienThe->stock <= 0 ? 'disabled' : '' }}
                           
                           {{-- Logic tự động chọn phiên bản đầu tiên CÒN HÀNG --}}
                           @if(!$hasChecked && $bienThe->stock > 0)
                               checked
                               @php $hasChecked = true; @endphp
                           @endif
                           
                           required>
                           
                    <span class="hop-phien-ban">
                        <strong class="d-block mb-1 text-dark">{{ $bienThe->edition }}</strong>
                        <small class="d-block text-muted mb-1">{{ number_format($bienThe->price) }} VNĐ</small>
                        <small class="{{ $bienThe->stock > 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                            {{ $bienThe->stock > 0 ? 'Còn ' . $bienThe->stock : 'Hết hàng' }}
                        </small>
                    </span>
                </label>
            @endif
        @endforeach
    </div>
</div>

                        <!-- Chọn Số Lượng -->
                        <div class="mb-4">
                            <h5 class="serif-font font-weight-bold mb-3">Số lượng :</h5>
                            <div class="input-group" style="width: 140px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary font-weight-bold" type="button" onclick="let q=document.getElementById('o-so-luong'); if(q.value>1)q.value--; q.dispatchEvent(new Event('input'))">-</button>
                                </div>
                                <input type="number" name="quantity" id="o-so-luong" class="form-control text-center font-weight-bold" value="1" min="1">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary font-weight-bold" type="button" onclick="let q=document.getElementById('o-so-luong'); q.value++; q.dispatchEvent(new Event('input'))">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Nút Hành Động -->
                        <div class="d-flex align-items-center mt-4 pt-3 border-top">
                            <button type="submit" name="action_type" value="add_to_cart" class="btn btn-dark rounded-pill px-4 py-3 font-weight-bold mr-2 shadow-sm">
                                <i class="fas fa-cart-plus mr-2"></i> Thêm vào giỏ
                            </button>

                            <button type="submit" name="action_type" value="buy_now" class="btn btn-orange rounded-pill px-4 py-3 font-weight-bold shadow-sm mr-2">
                                <i class="fas fa-bolt mr-2"></i> Mua ngay
                            </button>

                            <!-- Nút Wishlist -->
                            <button type="button" class="btn btn-outline-danger rounded-circle shadow-sm btn-wishlist" data-id="{{ $product->id }}" style="width: 55px; height: 55px;" title="Thêm vào yêu thích">
                                <i class="far fa-heart" style="font-size: 20px;"></i>
                            </button>
                        </div>   
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .chon-phien-ban input { display: none; }
    .hop-phien-ban { display: block; min-width: 150px; padding: 12px 15px; border: 2px solid #EEEEEE; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; background: #fff; }
    .chon-phien-ban input:checked + .hop-phien-ban { border: 2px solid var(--primary-color); background: #FFF6F0; }
    .chon-phien-ban input:disabled + .hop-phien-ban { opacity: 0.5; background: #F8F9FA; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right", "timeOut": "2500" };

    // Xử lý Wishlist
    document.querySelector('.btn-wishlist').addEventListener('click', function(e) {
        e.preventDefault();
        let btn = this;
        let productId = btn.getAttribute('data-id');
        let icon = btn.querySelector('i');

        axios.post('{{ route('user.wishlist.toggle') }}', {
            product_id: productId,
            _token: '{{ csrf_token() }}'
        })
        .then(function (response) {
            if(response.data.status === 'added') {
                icon.classList.remove('far'); icon.classList.add('fas');
                toastr.success(response.data.message);
            } else {
                icon.classList.remove('fas'); icon.classList.add('far');
                toastr.info(response.data.message);
            }
        })
        .catch(function (error) {
            if(error.response && error.response.status === 401) {
                toastr.warning("Đăng nhập để thêm vào danh sách yêu thích!");
            } else {
                toastr.error("Có lỗi xảy ra!");
            }
        });
    });

    // Xử lý giá và số lượng (Logic cũ của bạn)
    const danhSachRadio = document.querySelectorAll('input[name="product_variant_id"]');
    const oHienThiGia = document.getElementById('hien-thi-gia');
    const oSoLuong = document.getElementById('o-so-luong');
    const oForm = document.getElementById('them-vao-gio-hang');
    let tonKhoHienTai = 0;

    danhSachRadio.forEach(function (oRadio) {
        oRadio.addEventListener('change', function () {
            const gia = parseInt(this.dataset.gia);
            tonKhoHienTai = parseInt(this.dataset.tonKho);
            oHienThiGia.innerHTML = gia.toLocaleString('vi-VN') + ' VNĐ';
            oSoLuong.value = 1;
        });
    });

    oSoLuong.addEventListener('input', function () {
        if (parseInt(this.value) < 1 || isNaN(parseInt(this.value))) this.value = 1;
        if (tonKhoHienTai > 0 && parseInt(this.value) > tonKhoHienTai) this.value = tonKhoHienTai;
    });

    oForm.addEventListener('submit', function (e) {
        const bienTheDangChon = document.querySelector('input[name="product_variant_id"]:checked');
        if (!bienTheDangChon) { e.preventDefault(); alert('Vui lòng chọn phiên bản!'); return; }
    });
});
</script>
@endpush