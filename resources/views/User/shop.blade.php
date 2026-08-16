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
                    <!-- 3. Nếu KHÔNG CÓ danh mục -> Đang ở trang Tủ sách chung hoặc Tìm kiếm -> Tủ sách sáng lên -->
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
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header text-white font-weight-bold serif-font rounded-top" style="background-color: #2C3E50;">
                    <i class="fas fa-filter mr-2"></i> LỌC SÁCH
                </div>
                <div class="card-body bg-light">
                    <!-- Đổi action thành url()->current() để áp dụng cho mọi trang đang đứng -->
                    <form action="{{ url()->current() }}" method="GET">
                        
                        <!-- Giữ lại từ khóa tìm kiếm nếu có -->
                        @if(request('keyword'))
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        @endif
                        
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
                            <!-- Xóa lọc vẫn giữ lại URL hiện tại và từ khóa keyword -->
                            <a href="{{ url()->current() }}{{ request('keyword') ? '?keyword='.request('keyword') : '' }}" class="btn btn-block btn-outline-secondary mt-2">Xóa bộ lọc</a>
                        @endif
                    </form>
                </div>
            </div>    
        </aside>

        <!-- Cột phải: Vùng hiển thị sách -->
        <section class="col-lg-9">

            <!-- Hero Banner -->
            <section class="mt-4 mb-5">
                <style>
                    #heroCarousel{
                        border-radius:18px;
                        overflow:hidden;
                        box-shadow:0 12px 35px rgba(0,0,0,.12);
                    }
                    #heroCarousel .carousel-item img{
                        width:100%;
                        height:420px;
                        object-fit:cover;
                        object-position:center;
                    }
                    #heroCarousel .banner-overlay{
                        position:absolute;
                        inset:0;
                        background:linear-gradient(90deg, rgba(0,0,0,.65) 0%, rgba(0,0,0,.35) 45%, rgba(0,0,0,.08) 100%);
                    }
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
                </style>

                <div id="heroCarousel" class="carousel slide shadow" data-ride="carousel" data-interval="5000" data-pause="false" data-wrap="true">
                    <ol class="carousel-indicators">
                        @foreach($banners as $banner)
                            <li data-target="#heroCarousel" data-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>

                    <div class="carousel-inner">
                        @foreach($banners as $banner)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="position-relative">
                                    <img src="{{ asset('uploads/banners/'.$banner->image) }}" alt="{{ $banner->title }}">
                                    <div class="banner-overlay"></div>
                                    <div class="banner-content">
                                        <span class="badge badge-warning px-3 py-2 mb-3">BOOK STORE</span>
                                        <h2 class="banner-title">{{ $banner->title }}</h2>
                                        @if($banner->description)
                                            <div class="banner-desc">{{ $banner->description }}</div>
                                        @endif
                                        @if($banner->link)
                                            <a href="{{ $banner->link }}" class="btn btn-warning banner-btn shadow">
                                                Khám phá ngay <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </a>
                    <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </a>
                </div>
            </section>

            {{-- ================= TRANG CỬA HÀNG (HIỂN THỊ THEO DANH MỤC) ================= --}}
            @if(isset($sanPhamTheoDanhMuc))
                <div class="mb-4">
                    <h2 class="serif-font font-weight-bold">Khám phá Tủ sách</h2>
                    <p class="text-muted">Tuyển tập những cuốn sách hay nhất theo từng thể loại.</p>
                </div>

                @foreach($sanPhamTheoDanhMuc as $dm)
                    <div class="mb-5 bg-white p-4 rounded shadow-sm border">
                        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                            <h3 class="serif-font font-weight-bold mb-0" style="color:#2C3E50;">
                                {{ $dm->name }}
                            </h3>
                            <a href="{{ route('user.category',$dm->id) }}" class="text-decoration-none" style="color:var(--primary-color);font-weight:600;">
                                Xem thêm <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>

                        <div class="book-grid">
                            @foreach($dm->sanPham as $product)
                                <div class="position-relative book-card text-center">
                                    @if($product->firstVariant && $product->firstVariant->sale_price > 0 && $product->firstVariant->sale_price < $product->firstVariant->price)
                                        <span class="position-absolute d-flex align-items-center justify-content-center text-white" style="top:8px;left:8px;z-index:10;width:34px;height:34px;border-radius:50%;background:#D35400;font-size:10px;font-weight:700;">
                                            -{{ $product->firstVariant->discount_percent }}%
                                        </span>
                                    @endif

                                    <!-- Đổi thành btn-wishlist-v2 -->
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist-v2 position-absolute" data-id="{{ $product->id }}" style="top:10px;right:10px;width:34px;height:34px;">
                                        <i class="{{ in_array($product->id,$wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color:#D35400"></i>
                                    </button>

                                    <a href="{{ route('user.productDetails',$product->id) }}" class="text-decoration-none text-dark d-block">
                                        <img src="{{ asset('uploads/products/'.$product->image) }}" class="book-cover" alt="{{ $product->name }}">
                                        <h3 class="book-title mt-2">{{ $product->name }}</h3>

                                        @if($product->firstVariant && $product->firstVariant->sale_price > 0 && $product->firstVariant->sale_price < $product->firstVariant->price)
                                            <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                                                <span style="color:#dc3545;font-size:18px;font-weight:700;">
                                                    {{ number_format($product->firstVariant->sale_price,0,',','.') }} ₫
                                                </span>
                                                <span style="color:#999;font-size:14px;text-decoration:line-through;text-decoration-thickness:2px;">
                                                    {{ number_format($product->firstVariant->price,0,',','.') }} ₫
                                                </span>
                                            </div>
                                        @else
                                            <p style="color:#D35400;font-size:18px;font-weight:700;margin-top:8px;margin-bottom:0;">
                                                {{ number_format($product->price,0,',','.') }} ₫
                                            </p>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- ================= TRANG DANH MỤC / TÁC GIẢ / KẾT QUẢ TÌM KIẾM ================= --}}
            @if(isset($danhSachSanPham))
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h2 class="serif-font font-weight-bold mb-0" style="color:#2C3E50;">
                        @if(isset($author))
                            Sách của tác giả {{ $author->name }}
                        @elseif(isset($keyword))
                            Kết quả tìm kiếm cho: "{{ $keyword }}"
                        @else
                            {{ $danhMuc->name ?? 'Tất cả sản phẩm' }}
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
                        <h5 class="text-muted">Không tìm thấy tác phẩm nào.</h5>
                    </div>
                @else
                    <div class="book-grid">
                        @foreach($danhSachSanPham as $product)
                            <div class="position-relative book-card text-center">
                                @if($product->firstVariant && $product->firstVariant->sale_price > 0 && $product->firstVariant->sale_price < $product->firstVariant->price)
                                    <span class="position-absolute d-flex align-items-center justify-content-center text-white" style="top:8px;left:8px;z-index:10;width:34px;height:34px;border-radius:50%;background:#D35400;font-size:10px;font-weight:700;">
                                        -{{ $product->firstVariant->discount_percent }}%
                                    </span>
                                @endif

                                <!-- Đổi thành btn-wishlist-v2 -->
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist-v2 position-absolute" data-id="{{ $product->id }}" style="top:10px;right:10px;width:34px;height:34px;">
                                    <i class="{{ in_array($product->id,$wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color:#D35400"></i>
                                </button>

                                <a href="{{ route('user.productDetails',$product->id) }}" class="text-decoration-none text-dark d-block">
                                    <img src="{{ asset('uploads/products/'.$product->image) }}" class="book-cover" alt="{{ $product->name }}">
                                    <h3 class="book-title mt-2">{{ $product->name }}</h3>

                                    @if($product->firstVariant && $product->firstVariant->sale_price > 0 && $product->firstVariant->sale_price < $product->firstVariant->price)
                                        <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                                            <span style="color:#dc3545;font-size:18px;font-weight:700;">
                                                {{ number_format($product->firstVariant->sale_price,0,',','.') }} ₫
                                            </span>
                                            <span style="color:#999;font-size:14px;text-decoration:line-through;text-decoration-thickness:2px;">
                                                {{ number_format($product->firstVariant->price,0,',','.') }} ₫
                                            </span>
                                        </div>
                                    @else
                                        <p style="color:#D35400;font-size:18px;font-weight:700;margin-top:8px;margin-bottom:0;">
                                            {{ number_format($product->price,0,',','.') }} ₫
                                        </p>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 d-flex justify-content-center custom-pagination">
                        {{ $danhSachSanPham->appends(request()->query())->links() }}
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

    // Gọi theo class mới btn-wishlist-v2
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

            // 2. Chặn click đúp
            if (btn.classList.contains('is-loading')) return;
            btn.classList.add('is-loading');
            btn.style.opacity = '0.5';

            // Tìm toàn bộ các nút của CÙNG 1 SẢN PHẨM trên giao diện
            let allBtnsForThisProduct = document.querySelectorAll(`.btn-wishlist-v2[data-id="${productId}"]`);

            // 3. Xử lý qua AJAX
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