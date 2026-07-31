@extends('layout.user')

@section('content')
@php
    $wishlistIds = [];
    if(Auth::check()) {
        $wishlistIds = \Illuminate\Support\Facades\DB::table('wishlists')
            ->where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();
    }
@endphp

<!-- Hero Banner -->
<section class="container mt-4 mb-5">
    <div id="heroCarousel" class="carousel slide" data-ride="carousel" data-interval="4000" data-pause="false" data-wrap="true">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            @foreach($banners as $banner)
                <li data-target="#heroCarousel" data-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
            @endforeach
        </ol>

        <!-- Banner -->
        <div class="carousel-inner">
            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <div class="hero-item">
                    <img src="{{ asset('uploads/banners/'.$banner->image) }}" class="hero-image" alt="{{ $banner->title }}">
                    <!-- Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Nội dung -->
                    <div class="hero-content">
                        <span class="badge badge-warning px-3 py-2 mb-3">📚 BOOK STORE</span>
                        <h1>{{ $banner->title }}</h1>
                        @if($banner->description)
                            <p>{{ $banner->description }}</p>
                        @endif
                        @if($banner->link)
                            <a href="{{ $banner->link }}" class="btn btn-warning btn-lg hero-btn">
                                Khám phá ngay <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Previous -->
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <!-- Next -->
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</section>

<script>
$('#heroCarousel').carousel({
    interval: 5000,
    pause: false,
    wrap: true,
    keyboard: true
});
</script>

<!-- 2. Danh mục sách -->
<section class="container mb-5">
    <div class="d-flex flex-wrap justify-content-center gap-2">
        <a href="{{ route('user.shop') }}" class="btn rounded-pill mx-1 mb-2 px-4 text-white" style="background: #2C3E50;">Tất cả sách</a>
        @foreach ($categories as $category)
            <a href="{{ route('user.category', $category->id) }}" class="btn btn-light rounded-pill mx-1 mb-2 px-4 border shadow-sm text-dark">{{ $category->name }}</a>
        @endforeach
    </div>
</section>

<!-- 3. Tác phẩm nổi bật -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
        <h2 class="serif-font font-weight-bold mb-0">Tác phẩm nổi bật</h2>
        <a href="{{ route('user.shop') }}" class="text-muted text-decoration-none">
            Xem tất cả <i class="fas fa-angle-right"></i>
        </a>
    </div>

    @if($products->isEmpty())
        <div class="text-center text-muted py-5">Không tìm thấy sách nào.</div>
    @else
        <div class="book-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:20px;">
            @foreach($products as $product)
                <div class="book-card text-center position-relative">
                    {{-- Badge giảm giá --}}
                    @if($product->firstVariant && $product->firstVariant->sale_price > 0 && $product->firstVariant->sale_price < $product->firstVariant->price)
                        <span class="position-absolute d-flex align-items-center justify-content-center text-white"
                              style="top:8px;left:8px;z-index:10;width:36px;height:36px;border-radius:50%;background:#D35400;font-size:11px;font-weight:700;line-height:1;">
                            -{{ $product->firstVariant->discount_percent }}%
                        </span>
                    @endif

                    {{-- Wishlist Nút Mới (Đã đổi class thành btn-wishlist-v2 để né script cũ) --}}
                    <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist-v2 position-absolute"
                            data-id="{{ $product->id }}"
                            style="top:10px;right:10px;z-index:10;width:34px;height:34px;border:none;">
                        <i class="{{ in_array($product->id,$wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart"
                           style="color:#D35400;"></i>
                    </button>

                    <a href="{{ route('user.productDetails',$product->id) }}" class="text-decoration-none text-dark d-block">
                        <img src="{{ asset('uploads/products/'.$product->image) }}" class="book-cover" alt="{{ $product->name }}">
                        <h3 class="book-title mt-2">{{ $product->name }}</h3>

                        @if($product->firstVariant && $product->firstVariant->sale_price > 0 && $product->firstVariant->sale_price < $product->firstVariant->price)
                            <div class="d-flex justify-content-center align-items-center mt-2">
                                <span style="color:#dc3545;font-size:18px;font-weight:700;margin-right:8px;">
                                    {{ number_format($product->firstVariant->sale_price, 0, ',', '.') }} ₫
                                </span>
                                <span style="color:#999;font-size:15px;text-decoration:line-through;">
                                    {{ number_format($product->firstVariant->price, 0, ',', '.') }} ₫
                                </span>
                            </div>
                        @else
                            <p style="color:#D35400;font-size:18px;font-weight:700;margin-top:8px;">
                                {{ number_format($product->price, 0, ',', '.') }} ₫
                            </p>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- 4. Sách mới & Bán chạy -->
<section class="container mb-5 pt-4">
    <div class="row">
        <!-- Sách mới nhất -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h3 class="serif-font font-weight-bold mb-0"><i class="fas fa-clock mr-2" style="color: #D35400;"></i> Sách mới nhất</h3>
                <div>
                    <a href="#newBooksCarousel" role="button" data-slide="prev" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted"><i class="fas fa-chevron-left"></i></a>
                    <a href="#newBooksCarousel" role="button" data-slide="next" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
            <div id="newBooksCarousel" class="carousel slide" data-ride="carousel" data-interval="3500">
                <div class="carousel-inner bg-light p-4 rounded shadow-sm border" style="min-height: 240px;">
                    @foreach ($product5 as $index => $pro)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="position-relative">
                                <!-- Wishlist Nút Mới -->
                                <button class="btn btn-white rounded-circle shadow-sm btn-wishlist-v2 position-absolute" 
                                        data-id="{{ $pro->id }}" 
                                        style="top: 0; right: 0; z-index: 10; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: none; background: white;">
                                    <i class="{{ in_array($pro->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color: #D35400; font-size: 16px;"></i>
                                </button>

                                @if($pro->firstVariant && $pro->firstVariant->sale_price > 0 && $pro->firstVariant->sale_price < $pro->firstVariant->price)
                                    <span class="position-absolute d-flex align-items-center justify-content-center text-white"
                                          style="top:10px;left:10px;width:34px;height:34px;border-radius:50%;background:#D35400;font-size:10px;font-weight:700;z-index:10;">
                                        -{{ $pro->firstVariant->discount_percent }}%
                                    </span>
                                @endif
                                <a href="{{ route('user.productDetails', $pro->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                    <img src="{{ asset('uploads/products/' . $pro->image) }}" class="rounded shadow" style="width: 120px; height: 180px; object-fit: cover;">
                                    <div class="ml-4 pr-4">
                                        <span class="badge mb-2" style="background-color: #2C3E50; color: white;">MỚI PHÁT HÀNH</span>
                                        <h5 class="font-weight-bold mb-2" style="font-size: 18px; line-height: 1.4;">{{ $pro->name }}</h5>
                                        @if($pro->firstVariant && $pro->firstVariant->sale_price > 0 && $pro->firstVariant->sale_price < $pro->firstVariant->price)
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span style="color:#dc3545;font-size:22px;font-weight:700;">{{ number_format($pro->firstVariant->sale_price,0,',','.') }} ₫</span>
                                                <span style="color:#999;text-decoration:line-through;font-size:15px;">{{ number_format($pro->firstVariant->price,0,',','.') }} ₫</span>
                                            </div>
                                        @else
                                            <h4 class="mb-3" style="color:#D35400;font-weight:700;">{{ number_format($pro->price,0,',','.') }} ₫</h4>
                                        @endif
                                        <span class="btn btn-outline-dark btn-sm rounded-pill px-4">Mua ngay</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sách bán chạy -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h3 class="serif-font font-weight-bold mb-0"><i class="fas fa-fire mr-2" style="color: #e74c3c;"></i> Sách bán chạy</h3>
                <div>
                    <a href="#bestSellingCarousel" role="button" data-slide="prev" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted"><i class="fas fa-chevron-left"></i></a>
                    <a href="#bestSellingCarousel" role="button" data-slide="next" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
            <div id="bestSellingCarousel" class="carousel slide" data-ride="carousel" data-interval="4000">
                <div class="carousel-inner bg-light p-4 rounded shadow-sm border" style="min-height: 240px;">
                    @foreach ($topSanPham as $index => $pro)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="position-relative">
                                <!-- Wishlist Nút Mới -->
                                <button class="btn btn-white rounded-circle shadow-sm btn-wishlist-v2 position-absolute" 
                                        data-id="{{ $pro->id }}" 
                                        style="top: 0; right: 0; z-index: 10; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: none; background: white;">
                                    <i class="{{ in_array($pro->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color: #D35400; font-size: 16px;"></i>
                                </button>
                                @if($pro->firstVariant && $pro->firstVariant->sale_price > 0 && $pro->firstVariant->sale_price < $pro->firstVariant->price)
                                    <span class="position-absolute d-flex align-items-center justify-content-center text-white"
                                          style="top:10px;left:10px;width:34px;height:34px;border-radius:50%;background:#D35400;font-size:10px;font-weight:700;z-index:10;">
                                        -{{ $pro->firstVariant->discount_percent }}%
                                    </span>
                                @endif
                                <a href="{{ route('user.productDetails', $pro->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                    <img src="{{ asset('uploads/products/' . $pro->image) }}" class="rounded shadow" style="width: 120px; height: 180px; object-fit: cover;">
                                    <div class="ml-4 pr-4">
                                        <span class="badge mb-2" style="background-color: #e74c3c; color: white;">BEST SELLER</span>
                                        <h5 class="font-weight-bold mb-2" style="font-size: 18px; line-height: 1.4;">{{ $pro->name }}</h5>
                                        @if($pro->firstVariant && $pro->firstVariant->sale_price > 0 && $pro->firstVariant->sale_price < $pro->firstVariant->price)
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span style="color:#dc3545;font-size:22px;font-weight:700;">{{ number_format($pro->firstVariant->sale_price,0,',','.') }} ₫</span>
                                                <span style="color:#999;text-decoration:line-through;font-size:15px;">{{ number_format($pro->firstVariant->price,0,',','.') }} ₫</span>
                                            </div>
                                        @else
                                            <h4 class="mb-3" style="color:#D35400;font-weight:700;">{{ number_format($pro->price,0,',','.') }} ₫</h4>
                                        @endif
                                        <span class="btn btn-outline-dark btn-sm rounded-pill px-4">Đọc ngay</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cấu hình Toastr
    toastr.options = { 
        "closeButton": true, 
        "progressBar": true, 
        "positionClass": "toast-bottom-right", 
        "timeOut": "2500" 
    };

    // Đã đổi class thành btn-wishlist-v2, đảm bảo script cũ (btn-wishlist) bị vô hiệu hóa
    const wishlistButtons = document.querySelectorAll('.btn-wishlist-v2');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation();

            // 1. Cảnh báo Đăng nhập bằng SweetAlert2
            @if(!Auth::check())
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa đăng nhập',
                    text: 'Bạn cần đăng nhập để thêm sách vào danh sách yêu thích!',
                    showCancelButton: true,
                    confirmButtonText: 'Đăng nhập ngay',
                    cancelButtonText: 'Để sau',
                    confirmButtonColor: '#D35400',
                    cancelButtonColor: '#2C3E50'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
                return;
            @endif

            let btn = this;
            let productId = btn.getAttribute('data-id');

            // 2. Chặn spam click liên tục (khóa nút)
            if (btn.classList.contains('is-loading')) return;
            btn.classList.add('is-loading');
            btn.style.opacity = '0.5';

            // Tìm TẤT CẢ các nút của CÙNG 1 SẢN PHẨM trên màn hình để update đồng loạt
            let allBtnsForThisProduct = document.querySelectorAll(`.btn-wishlist-v2[data-id="${productId}"]`);

            // 3. Xử lý Thả tim qua AJAX
            axios.post('{{ route('user.wishlist.toggle') }}', {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            })
            .then(function (response) {
                if(response.data.status === 'added') {
                    allBtnsForThisProduct.forEach(b => {
                        let icon = b.querySelector('i');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    });
                    toastr.success(response.data.message);
                } else {
                    allBtnsForThisProduct.forEach(b => {
                        let icon = b.querySelector('i');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    });
                    toastr.info(response.data.message);
                }
            })
            .catch(function (error) {
                console.error(error);
                toastr.error("Có lỗi xảy ra, vui lòng thử lại!");
            })
            .finally(function() {
                // 4. Mở khóa nút
                btn.classList.remove('is-loading');
                btn.style.opacity = '1';
            });
        });
    });
});
</script>
@endpush