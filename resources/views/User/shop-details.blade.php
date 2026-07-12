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
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">Tác giả: {{ $product->author->name }}</p>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">NXB: {{ $product->publishers->name }}</p>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">Mô tả: {{ $product->description }}</p>

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

        {{-- Đánh giá & Nhận xét --}}
            <div class="card p-3 border-0 shadow-sm rounded-3 mt-4">
                <h5 class="serif-font font-weight-bold mb-1">Đánh giá & Nhận xét</h5>
                <div class="border-top pt-3 mt-3">  
                    <div class="row align-items-center">
                        <!-- Cột 1: Điểm số trung bình (Diện tích col-md-2 - chữ thu nhỏ) -->
                        <div class="col-md-2 text-center border-end py-1">
                            <!-- Hạ font chữ xuống fs-2 để không bị quá to chiếm diện tích -->
                            <h2 class="fw-bold text-dark mb-0">0<span class="fs-6 text-muted">/5</span></h2>
                            
                            <div class="text-muted fs-6 my-1">
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">(0 đánh giá)</p>
                        </div>

                        <!-- Cột 2: Đồ thị phần trăm (Bóp nhỏ lại còn col-md-3) -->
                        <div class="col-md-3 px-2 border-end py-1">
                            <div class="d-flex align-items-center mb-1" style="font-size: 0.8rem;">
                                <span style="width: 40px;" class="text-muted">5 sao</span>
                                <div class="progress flex-grow-1 mx-2" style="height: 5px; background-color: #f0f2f5;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                                </div>
                                <span style="width: 25px;" class="text-end text-muted">0%</span>
                            </div>

                            <div class="d-flex align-items-center mb-1" style="font-size: 0.8rem;">
                                <span style="width: 40px;" class="text-muted">4 sao</span>
                                <div class="progress flex-grow-1 mx-2" style="height: 5px; background-color: #f0f2f5;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                                </div>
                                <span style="width: 25px;" class="text-end text-muted">0%</span>
                            </div>

                            <div class="d-flex align-items-center mb-1" style="font-size: 0.8rem;">
                                <span style="width: 40px;" class="text-muted">3 sao</span>
                                <div class="progress flex-grow-1 mx-2" style="height: 5px; background-color: #f0f2f5;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                                </div>
                                <span style="width: 25px;" class="text-end text-muted">0%</span>
                            </div>

                            <div class="d-flex align-items-center mb-1" style="font-size: 0.8rem;">
                                <span style="width: 40px;" class="text-muted">2 sao</span>
                                <div class="progress flex-grow-1 mx-2" style="height: 5px; background-color: #f0f2f5;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                                </div>
                                <span style="width: 25px;" class="text-end text-muted">0%</span>
                            </div>

                            <div class="d-flex align-items-center" style="font-size: 0.8rem;">
                                <span style="width: 40px;" class="text-muted">1 sao</span>
                                <div class="progress flex-grow-1 mx-2" style="height: 5px; background-color: #f0f2f5;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                                </div>
                                <span style="width: 25px;" class="text-end text-muted">0%</span>
                            </div>
                        </div>

                        <!-- Cột 3: Phần nội dung bình luận rộng rãi nhất (Chiếm col-md-7) -->
                        <div class="col-md-7 ps-4 text-start py-1">
                            <p class="text-muted mb-0 small">
                                Chỉ có thành viên mới có thể viết nhận xét. Vui lòng 
                                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">đăng nhập</a> hoặc 
                                <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">đăng ký</a>.
                            </p>
                        </div>
                        
                    </div>
                </div>
    
{{-- Sách cùng danh mục --}}
    </div>
   <div class="mb-5 bg-white p-4 rounded shadow-sm border mt-4">

    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
        <h2 class="serif-font font-weight-bold mb-0">
            Sách cùng danh mục
        </h2>

        <a href="{{ route('user.category', $product->category_id) }}"
   class="text-muted text-decoration-none">
    Xem tất cả <i class="fas fa-angle-right"></i>
</a>
    </div>

    @if($relatedProducts->isEmpty())

        <div class="text-center py-5 text-muted">
            Chưa có sách cùng danh mục.
        </div>

    @else

        <div class="book-grid">

            @foreach($relatedProducts as $product)

                <div class="book-card text-center position-relative">

                    <button
                        class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist position-absolute"
                        data-id="{{ $product->id }}"
                        style="top:10px;right:10px;width:34px;height:34px;">
                        <i class="{{ in_array($product->id,$wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart"
                           style="color:#D35400"></i>
                    </button>

                    <a href="{{ route('user.productDetails',$product->id) }}"
                       class="text-decoration-none text-dark d-block">

                        <img src="{{ asset('uploads/products/'.$product->image) }}"
                             class="book-cover"
                             alt="{{ $product->name }}">

                        <h3 class="book-title mt-2">
                            {{ $product->name }}
                        </h3>

                        <p class="book-price">
                            {{ number_format($product->price,0,',','.') }} ₫
                        </p>

                    </a>

                </div>

            @endforeach

        </div>

    @endif

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