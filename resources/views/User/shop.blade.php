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

<!-- Breadcrumb -->
<div class="bg-light py-3 mb-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <!-- 1. Trang chủ luôn là link mờ -->
                <li class="breadcrumb-item">
                    <a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a>
                </li>

                @if(isset($danhMuc))
                    <!-- 2. Nếu ĐANG CÓ danh mục -> Tủ sách là link mờ, Danh mục sáng lên -->
                    <li class="breadcrumb-item">
                        <a href="{{ route('user.shop') }}" class="text-muted">Tủ sách</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">
                        {{ $danhMuc->name }}
                    </li>
                @else
                    <!-- 3. Nếu KHÔNG CÓ danh mục -> Đang ở trang Tủ sách chung -> Tủ sách sáng lên -->
                    <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">
                        Tủ sách
                    </li>
                @endif
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Cột trái: Bộ lọc (Sidebar) -->
        <aside class="col-lg-3 mb-4">
            <!-- Box Thể Loại -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header text-white font-weight-bold serif-font rounded-top" style="background-color: #2C3E50;">
                    <i class="fas fa-list mr-2"></i> THỂ LOẠI
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($tatCaDanhMuc as $cat)
                        <li class="list-group-item bg-light border-bottom border-white">
                            <a href="{{ route('user.category', $cat->id) }}" class="text-decoration-none d-block {{ isset($danhMuc) && $danhMuc->id == $cat->id ? 'font-weight-bold' : 'text-dark' }}" style="{{ isset($danhMuc) && $danhMuc->id == $cat->id ? 'color: var(--primary-color) !important;' : '' }}">
                                <i class="fas fa-angle-right mr-2 text-muted"></i> {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Box Lọc -->
            @if(isset($danhMuc))
                <div class="card border-0 shadow-sm rounded mb-4">
                    <div class="card-header text-white font-weight-bold serif-font rounded-top" style="background-color: #2C3E50;">
                        <i class="fas fa-filter mr-2"></i> LỌC SÁCH
                    </div>
                    <div class="card-body bg-light">
                        <form action="{{ route('user.category', $danhMuc->id) }}" method="GET">
                            
                            <!-- Sắp xếp -->
                            <h6 class="font-weight-bold text-uppercase text-muted mb-2" style="font-size: 13px;">Sắp xếp theo</h6>
                            <select name="sort" class="form-control form-control-sm mb-3">
                                <option value="">Mặc định</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao xuống thấp</option>
                            </select>

                            <!-- Khoảng giá -->
                            <h6 class="font-weight-bold text-uppercase text-muted mb-2 mt-3" style="font-size: 13px;">Khoảng giá (VNĐ)</h6>
                            <div class="d-flex align-items-center mb-3 gap-2">
                                <input type="number" name="price_min" value="{{ request('price_min') }}" class="form-control form-control-sm text-center" placeholder="Từ">
                                <span class="text-muted mx-1">-</span>
                                <input type="number" name="price_max" value="{{ request('price_max') }}" class="form-control form-control-sm text-center" placeholder="Đến">
                            </div>

                            <!-- Tác giả -->
                            <h6 class="font-weight-bold text-uppercase text-muted mb-2 mt-3" style="font-size: 13px;">Tác giả</h6>
                            <select name="author" class="form-control form-control-sm mb-3">
                                <option value="">Tất cả</option>
                                @foreach($tacGia as $tg)
                                    <option value="{{ $tg->id }}" {{ request('author') == $tg->id ? 'selected' : '' }}>{{ $tg->name }}</option>
                                @endforeach
                            </select>

                            <!-- NXB -->
                            <h6 class="font-weight-bold text-uppercase text-muted mb-2 mt-3" style="font-size: 13px;">Nhà xuất bản</h6>
                            <select name="publisher" class="form-control form-control-sm mb-4">
                                <option value="">Tất cả</option>
                                @foreach($nhaXuatBan as $nxb)
                                    <option value="{{ $nxb->id }}" {{ request('publisher') == $nxb->id ? 'selected' : '' }}>{{ $nxb->name }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-block text-white font-weight-bold" style="background-color: var(--primary-color);">Áp dụng bộ lọc</button>
                            
                            @if(request()->anyFilled(['price_min', 'price_max', 'author', 'publisher', 'sort']))
                                <a href="{{ route('user.category', $danhMuc->id) }}" class="btn btn-block btn-outline-secondary mt-2">Xóa bộ lọc</a>
                            @endif
                        </form>
                    </div>
                </div>
            @endif
        </aside>

        <!-- Cột phải: Vùng hiển thị sách -->
    <section class="col-lg-9">

<!-- Hero Banner -->
<section class="container mt-4 mb-5">

    <style>
       #heroCarousel{
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 12px 35px rgba(0,0,0,.12);
}

/* Ảnh */
#heroCarousel .carousel-item img{
    width:100%;
    height:420px;
    object-fit:cover;
    object-position:center;
}

/* Overlay */
#heroCarousel .banner-overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(
        90deg,
        rgba(0,0,0,.65) 0%,
        rgba(0,0,0,.35) 45%,
        rgba(0,0,0,.08) 100%
    );
}

/* Nội dung */
#heroCarousel .banner-content{
    position:absolute;
    top:50%;
    left:60px;
    transform:translateY(-50%);
    max-width:480px;
    color:#fff;
}

#heroCarousel .banner-title{
    font-size:42px;
    font-weight:700;
    line-height:1.25;
    margin-bottom:15px;
    text-shadow:0 3px 12px rgba(0,0,0,.35);
}

#heroCarousel .banner-desc{
    font-size:17px;
    line-height:1.7;
    color:#f3f3f3;
    margin-bottom:28px;
}

#heroCarousel .banner-btn{
    padding:12px 30px;
    border-radius:40px;
    font-weight:600;
    transition:.3s;
}

#heroCarousel .banner-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(255,193,7,.35);
}

/* ===== Arrow ===== */

#heroCarousel .carousel-control-prev,
#heroCarousel .carousel-control-next{
    width:70px;
    opacity:0;
    transition:.3s;
}

#heroCarousel:hover .carousel-control-prev,
#heroCarousel:hover .carousel-control-next{
    opacity:1;
}

#heroCarousel .carousel-control-prev-icon,
#heroCarousel .carousel-control-next-icon{
    width:48px;
    height:48px;
    border-radius:50%;
    background-color:rgba(255,255,255,.22);
    backdrop-filter:blur(4px);
    background-size:40%;
}

/* ===== Indicator ===== */

#heroCarousel .carousel-indicators{
    bottom:15px;
    margin-bottom:0;
}

#heroCarousel .carousel-indicators li{
    width:8px;
    height:8px;
    margin:0 5px;
    border:none;
    border-radius:50%;
    background:#fff;
    opacity:.45;
    transition:.3s;
}

#heroCarousel .carousel-indicators .active{
    width:22px;
    border-radius:20px;
    background:#ffc107;
    opacity:1;
}

/* Responsive */

@media(max-width:992px){

    #heroCarousel .carousel-item img{
        height:320px;
    }

    #heroCarousel .banner-content{
        left:35px;
        max-width:360px;
    }

    #heroCarousel .banner-title{
        font-size:30px;
    }

}

@media(max-width:768px){

    #heroCarousel .carousel-item img{
        height:220px;
    }

    #heroCarousel .banner-content{
        left:20px;
        right:20px;
        max-width:100%;
    }

    #heroCarousel .banner-title{
        font-size:22px;
    }

    #heroCarousel .banner-desc{
        font-size:14px;
    }

    #heroCarousel .banner-btn{
        padding:8px 18px;
        font-size:14px;
    }

}
    </style>

    <div id="heroCarousel"
     class="carousel slide shadow"
     data-ride="carousel"
     data-interval="5000"
     data-pause="false"
     data-wrap="true">

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
                             alt="{{ $banner->title }}">

                        <div class="banner-overlay"></div>

                        <div class="banner-content">

                            <span class="badge badge-warning px-3 py-2 mb-3">
                                BOOK STORE
                            </span>

                            <h2 class="banner-title">
                                {{ $banner->title }}
                            </h2>

                            @if($banner->description)
                                <div class="banner-desc">
                                    {{ $banner->description }}
                                </div>
                            @endif

                            @if($banner->link)
                                <a href="{{ $banner->link }}"
                                   class="btn btn-warning banner-btn shadow">
                                    Khám phá ngay
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            @endif

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

        <a class="carousel-control-prev"
           href="#heroCarousel"
           role="button"
           data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>

        <a class="carousel-control-next"
           href="#heroCarousel"
           role="button"
           data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>

    </div>

</section>
    {{-- ================= TRANG CỬA HÀNG ================= --}}
    @if(isset($sanPhamTheoDanhMuc))

        <div class="mb-4">
            <h2 class="serif-font font-weight-bold">Khám phá Tủ sách</h2>
            <p class="text-muted">
                Tuyển tập những cuốn sách hay nhất theo từng thể loại.
            </p>
        </div>

        @foreach($sanPhamTheoDanhMuc as $dm)

            <div class="mb-5 bg-white p-4 rounded shadow-sm border">

                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">

                    <h3 class="serif-font font-weight-bold mb-0"
                        style="color:#2C3E50;">
                        {{ $dm->name }}
                    </h3>

                    <a href="{{ route('user.category',$dm->id) }}"
                       class="text-decoration-none"
                       style="color:var(--primary-color);font-weight:600;">
                        Xem thêm
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>

                </div>

                <div class="book-grid">

                    @foreach($dm->sanPham as $product)

                        <div class="position-relative book-card text-center">

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

            </div>

        @endforeach

    @endif



    {{-- ================= TRANG DANH MỤC / TÁC GIẢ ================= --}}
    @if(isset($danhSachSanPham))

        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">

            <h2 class="serif-font font-weight-bold mb-0"
                style="color:#2C3E50;">

                @if(isset($author))
                    Sách của tác giả {{ $author->name }}
                @else
                    {{ $danhMuc->name }}
                @endif

            </h2>

            <span class="text-muted">
                <i class="fas fa-book mr-1"></i>
                {{ $danhSachSanPham->total() }} tác phẩm
            </span>

        </div>

        @if($danhSachSanPham->isEmpty())

            <div class="text-center py-5 bg-light rounded shadow-sm border">

                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>

                <h5 class="text-muted">
                    Chưa có tác phẩm nào.
                </h5>

            </div>

        @else

            <div class="book-grid">

                @foreach($danhSachSanPham as $product)

                    <div class="position-relative book-card text-center">

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

            <div class="mt-5 d-flex justify-content-center custom-pagination">
                {{ $danhSachSanPham->links() }}
            </div>

        @endif

    @endif

</section>
    </div>
</div>

<!-- Tùy chỉnh CSS -->
<style>
    .custom-pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    .custom-pagination .page-link {
        color: #2C3E50;
    }
</style>
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
<script>
$(document).ready(function () {
    $('#heroCarousel').carousel({
        interval: 5000,
        pause: false,
        wrap: true,
        keyboard: true
    });
});
</script>
@endpush