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
@endpush