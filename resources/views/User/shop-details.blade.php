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
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">Danh mục: {{ $product->category->name }}</p>
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

                            <button type="submit" name="action_type" value="buy_now" class="btn btn-orange rounded-pill px-4 py-3 font-weight-bold shadow-sm mr-2" style="background-color: var(--primary-color); color: #fff;">
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
        <div class="card p-3 p-md-4 border-0 shadow-sm rounded-3 mt-4">
            <h5 class="serif-font font-weight-bold mb-1">Đánh giá & Nhận xét</h5>
            <div class="border-top pt-4 mt-3">  
                <div class="row align-items-center mb-4">
                    <!-- Cột 1: Điểm số trung bình -->
                    <div class="col-md-3 col-lg-2 text-center border-end py-2">
                        <h2 class="fw-bold text-dark mb-0">0<span class="fs-6 text-muted">/5</span></h2>
                        <div class="text-warning fs-6 my-1">
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                        <p class="text-muted small mb-0" style="font-size: 0.85rem;">(0 đánh giá)</p>
                    </div>

                    <!-- Cột 2: Đồ thị phần trăm -->
                    <div class="col-md-4 col-lg-3 px-3 border-end py-2">
                        @for ($i = 5; $i >= 1; $i--)
                        <div class="d-flex align-items-center mb-1" style="font-size: 0.85rem;">
                            <span style="width: 40px;" class="text-muted">{{ $i }} sao</span>
                            <div class="progress flex-grow-1 mx-2" style="height: 6px; background-color: #f0f2f5;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                            </div>
                            <span style="width: 30px;" class="text-end text-muted">0%</span>
                        </div>
                        @endfor
                    </div>

                    <!-- Cột 3: Trạng thái / Form đánh giá -->
                    <div class="col-md-5 col-lg-7 ps-md-4 text-start py-2">
                        @guest
                            <div class="bg-light p-3 rounded text-center">
                                <p class="text-muted mb-2">Chỉ có thành viên mới có thể viết nhận xét.</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Đăng nhập</a>
                                <span class="mx-2 text-muted">hoặc</span>
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold">Đăng ký</a>
                            </div>
                        @else
                            <form action="#" method="POST" id="form-danh-gia">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <h6 class="font-weight-bold mb-2">Gửi đánh giá của bạn</h6>
                                
                                <!-- Star Rating Input -->
                                <div class="star-rating mb-2">
                                    <input type="radio" id="star5" name="rating" value="5"><label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                                </div>

                                <textarea class="form-control mb-2 shadow-sm" name="comment" rows="2" placeholder="Nhập nhận xét của bạn về sản phẩm này..." required style="resize: none; font-size: 0.9rem;"></textarea>
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4">Gửi nhận xét</button>
                            </form>
                        @endguest
                    </div>
                </div>

                <!-- Danh sách bình luận -->
                <div class="comments-list mt-4 pt-3 border-top">
                    {{-- Ví dụ vòng lặp lấy comments (Bạn cần điều chỉnh lại relation $product->reviews tuỳ theo DB của bạn) --}}
                    {{-- @forelse($product->reviews as $review) --}}
                    
                    {{-- Placeholder khi chưa có bình luận thật --}}
                    <div class="text-center py-4 text-muted">
                        <i class="far fa-comments fa-3x mb-3" style="color: #dee2e6;"></i>
                        <p>Chưa có đánh giá nào. Hãy là người đầu tiên nhận xét về cuốn sách này!</p>
                    </div>

                    {{-- Giao diện mẫu 1 bình luận (Khi có dữ liệu, hãy bỏ comment vòng lặp phía trên để sử dụng) --}}
                    <!-- 
                    <div class="d-flex mb-4">
                        <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit: cover;">
                        <div class="ml-3 pl-2 w-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold mb-0">Nguyễn Văn A</h6>
                                <small class="text-muted">12/07/2026</small>
                            </div>
                            <div class="text-warning mb-2" style="font-size: 0.85rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Sách rất hay, giao hàng nhanh chóng. Bọc cẩn thận. Sẽ ủng hộ shop dài dài!</p>
                        </div>
                    </div> 
                    <hr class="text-muted" style="opacity: 0.1">
                    -->

                    {{-- @empty --}}
                    {{-- @endforelse --}}
                </div>

            </div>
        </div>

        {{-- Sách cùng danh mục --}}
        <div class="mb-5 bg-white p-4 rounded shadow-sm border mt-4">
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                <h2 class="serif-font font-weight-bold mb-0">
                    Sách của tác giả {{ $product->author->name }}
                </h2>
                <a href="{{ route('user.author', $product->author_id) }}" class="text-muted text-decoration-none">
                    Xem tất cả
                </a>
            </div>

            @if($relatedProducts->isEmpty())
                <div class="text-center py-5 text-muted">
                    Chưa có sách cùng danh mục.
                </div>
            @else
                <div class="row">
                    @foreach($relatedProducts as $item)
                        <div class="col-lg-2 col-md-3 col-6 mb-4">
                            <div class="card border-0 h-100 text-center">
                                <a href="{{ route('user.productDetails',$item->id) }}" class="d-flex justify-content-center mt-2">
                                    <img src="{{ asset('uploads/products/'.$item->image) }}" class="rounded shadow" style="width:120px; height:180px; object-fit:cover;" alt="{{ $item->name }}">
                                </a>
                                <div class="card-body p-2">
                                    <h6 class="mb-2 text-truncate">
                                        {{ $item->name }}
                                    </h6>
                                    <div class="font-weight-bold" style="color:#D35400;">
                                        {{ number_format($item->price,0,',','.') }} ₫
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    /* Radio box chọn phiên bản */
    .chon-phien-ban input { display: none; }
    .hop-phien-ban { display: block; min-width: 150px; padding: 12px 15px; border: 2px solid #EEEEEE; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; background: #fff; }
    .chon-phien-ban input:checked + .hop-phien-ban { border: 2px solid var(--primary-color); background: #FFF6F0; }
    .chon-phien-ban input:disabled + .hop-phien-ban { opacity: 0.5; background: #F8F9FA; cursor: not-allowed; }

    /* Star Rating tương tác */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        color: #ddd;
        font-size: 1.5rem;
        padding: 0 0.1rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }
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

    // Xử lý giá và số lượng
    const danhSachRadio = document.querySelectorAll('input[name="product_variant_id"]');
    const oHienThiGia = document.getElementById('hien-thi-gia');
    const oSoLuong = document.getElementById('o-so-luong');
    const oForm = document.getElementById('them-vao-gio-hang');
    let tonKhoHienTai = 0;

    // Load tồn kho của bản được checked mặc định
    const defaultChecked = document.querySelector('input[name="product_variant_id"]:checked');
    if (defaultChecked) {
        tonKhoHienTai = parseInt(defaultChecked.dataset.tonKho);
    }

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

// Carousel (Nếu bạn có file OWL Carousel được import ở header)
if(typeof $('.related-carousel').owlCarousel === 'function') {
    $('.related-carousel').owlCarousel({
        loop:true,
        margin:20,
        nav:true,
        dots:false,
        responsive:{
            0:{items:2},
            576:{items:3},
            768:{items:4},
            992:{items:5}
        }
    });
}
</script>
@endpush