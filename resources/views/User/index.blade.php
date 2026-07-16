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
    <div id="heroCarousel"
         class="carousel slide carousel-fade shadow-lg"
         data-ride="carousel"
         data-interval="5000"
         style="border-radius:18px;overflow:hidden;">

        <ol class="carousel-indicators">
            @foreach($banners as $banner)
                <li data-target="#heroCarousel"
                    data-slide-to="{{ $loop->index }}"
                    class="{{ $loop->first ? 'active' : '' }}">
                </li>
            @endforeach
        </ol>

        <div class="carousel-inner">

            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">

                <div class="position-relative">

                    <img src="{{ asset('uploads/banners/'.$banner->image) }}"
                         class="w-100"
                         style="
                            height:520px;
                            object-fit:cover;
                            object-position:center;
                         ">

                    <!-- Overlay -->
                    <div style="
                        position:absolute;
                        inset:0;
                        background:linear-gradient(
                            90deg,
                            rgba(0,0,0,.75) 0%,
                            rgba(0,0,0,.35) 45%,
                            rgba(0,0,0,.05) 100%
                        );
                    "></div>

                    <!-- Nội dung -->
                    <div class="position-absolute"
                         style="
                            left:70px;
                            top:50%;
                            transform:translateY(-50%);
                            max-width:520px;
                            color:#fff;
                         ">

                        <span class="badge badge-warning px-3 py-2 mb-3">
                            BOOK STORE
                        </span>

                        <h1 class="font-weight-bold mb-3"
                            style="
                                font-size:52px;
                                line-height:1.2;
                                text-shadow:0 3px 12px rgba(0,0,0,.4);
                            ">
                            {{ $banner->title }}
                        </h1>

                        @if($banner->description)
                            <p class="mb-4"
                               style="
                                  font-size:18px;
                                  color:#f1f1f1;
                                  line-height:1.8;
                               ">
                                {{ $banner->description }}
                            </p>
                        @endif

                        @if($banner->link)
                            <a href="{{ $banner->link }}"
                               class="btn btn-warning btn-lg rounded-pill px-5 shadow">
                                Khám phá ngay
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        @endif

                    </div>

                </div>

            </div>
            @endforeach

        </div>

        <a class="carousel-control-prev" href="#heroCarousel" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>

        <a class="carousel-control-next" href="#heroCarousel" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>

    </div>
</section>

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
        <a href="{{ route('user.shop') }}" class="text-muted text-decoration-none">Xem tất cả <i class="fas fa-angle-right"></i></a>
    </div>
    
    @if($products->isEmpty())
        <div class="text-center text-muted py-5">Không tìm thấy sách nào.</div>
    @else
        <div class="book-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            @foreach ($products as $product)
                <div class="book-card text-center position-relative">
                    <!-- Nút Wishlist -->
                    <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $product->id }}" style="top: 10px; right: 10px; z-index: 10; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: none;">
                        <i class="{{ in_array($product->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color: #D35400; font-size: 16px;"></i>
                    </button>

                    <a href="{{ route('user.productDetails', $product->id) }}" class="text-decoration-none text-dark d-block">
                        <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover" style="width:100%; height:auto;" alt="{{ $product->name }}">
                        <h3 class="book-title mt-2" title="{{ $product->name }}">{{ $product->name }}</h3>
                        <p class="book-price">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
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
                                <!-- Nút Wishlist -->
                                <button class="btn btn-white rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $pro->id }}" style="top: 0; right: 0; z-index: 10; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: none; background: white;">
                                    <i class="{{ in_array($pro->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color: #D35400; font-size: 16px;"></i>
                                </button>
                                <a href="{{ route('user.productDetails', $pro->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                    <img src="{{ asset('uploads/products/' . $pro->image) }}" class="rounded shadow" style="width: 120px; height: 180px; object-fit: cover;">
                                    <div class="ml-4 pr-4">
                                        <span class="badge mb-2" style="background-color: #2C3E50; color: white;">MỚI PHÁT HÀNH</span>
                                        <h5 class="font-weight-bold mb-2" style="font-size: 18px; line-height: 1.4;">{{ $pro->name }}</h5>
                                        <h4 class="mb-3" style="color: #D35400; font-weight: 700;">{{ number_format($pro->price, 0, ',', '.') }} ₫</h4>
                                        <span class="btn btn-outline-dark btn-sm rounded-pill px-4">Đọc ngay</span>
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
                                <!-- Nút Wishlist -->
                                <button class="btn btn-white rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $pro->id }}" style="top: 0; right: 0; z-index: 10; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: none; background: white;">
                                    <i class="{{ in_array($pro->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color: #D35400; font-size: 16px;"></i>
                                </button>
                                <a href="{{ route('user.productDetails', $pro->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                    <img src="{{ asset('uploads/products/' . $pro->image) }}" class="rounded shadow" style="width: 120px; height: 180px; object-fit: cover;">
                                    <div class="ml-4 pr-4">
                                        <span class="badge mb-2" style="background-color: #e74c3c; color: white;">BEST SELLER</span>
                                        <h5 class="font-weight-bold mb-2" style="font-size: 18px; line-height: 1.4;">{{ $pro->name }}</h5>
                                        <h4 class="mb-3" style="color: #D35400; font-weight: 700;">{{ number_format($pro->price, 0, ',', '.') }} ₫</h4>
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
<!-- Thư viện cho Wishlist -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function(){
    // Cấu hình Toastr
    toastr.options = { 
        "closeButton": true, 
        "progressBar": true, 
        "positionClass": "toast-bottom-right", 
        "timeOut": "2500" 
    };

    $('.btn-wishlist').click(function(e) {
        e.preventDefault(); 
        
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

        // 2. Xử lý Thả tim qua AJAX
        let btn = $(this);
        let productId = btn.data('id');
        let icon = btn.find('i');

        axios.post('{{ route('user.wishlist.toggle') }}', {
            product_id: productId,
            _token: '{{ csrf_token() }}'
        })
        .then(function (response) {
            if(response.data.status === 'added') {
                icon.removeClass('far').addClass('fas'); 
                toastr.success(response.data.message);
            } else {
                icon.removeClass('fas').addClass('far');
                toastr.info(response.data.message);
            }
        })
        .catch(function (error) {
            console.error(error);
            toastr.error("Có lỗi xảy ra, vui lòng thử lại!");
        });
    });
});
</script>
@endpush