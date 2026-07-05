@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 mb-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.shop') }}" class="text-muted">Tủ sách</a></li>
                @if(isset($danhMuc))
                    <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">{{ $danhMuc->name }}</li>
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

            <!-- Box Lọc (Chỉ hiển thị nút Lọc khi ở trong Trang Danh Mục vì hàm index không xử lý lọc) -->
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
            
            {{-- TRƯỜNG HỢP 1: TRANG CỬA HÀNG CHUNG (Nhóm theo danh mục) --}}
            @if(isset($sanPhamTheoDanhMuc))
                <div class="mb-4">
                    <h2 class="serif-font font-weight-bold">Khám phá Tủ sách</h2>
                    <p class="text-muted">Tuyển tập những cuốn sách hay nhất theo từng thể loại.</p>
                </div>

                @foreach($sanPhamTheoDanhMuc as $dm)
                    <div class="mb-5 bg-white p-4 rounded shadow-sm border">
                        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                            <h3 class="serif-font font-weight-bold mb-0" style="color: #2C3E50;">{{ $dm->name }}</h3>
                            <a href="{{ route('user.category', $dm->id) }}" class="text-decoration-none" style="color: var(--primary-color); font-weight: 600;">Xem thêm <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                        
                        <div class="book-grid">
                            @foreach($dm->sanPham as $product)
                                <a href="{{ route('user.productDetails', $product->id) }}" class="book-card text-center text-dark text-decoration-none">
                                    <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover" alt="{{ $product->name }}">
                                    <h3 class="book-title" title="{{ $product->name }}">{{ $product->name }}</h3>
                                    <p class="book-price">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- TRƯỜNG HỢP 2: TRANG CHI TIẾT 1 DANH MỤC (Có phân trang, bộ lọc) --}}
            @if(isset($danhSachSanPham))
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h2 class="serif-font font-weight-bold mb-0" style="color: #2C3E50;">{{ $danhMuc->name }}</h2>
                    <span class="text-muted"><i class="fas fa-book mr-1"></i> {{ $danhSachSanPham->total() }} tác phẩm</span>
                </div>

                @if($danhSachSanPham->isEmpty())
                    <div class="text-center py-5 bg-light rounded shadow-sm border">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có tác phẩm nào phù hợp với bộ lọc.</h5>
                    </div>
                @else
                    <div class="book-grid">
                        @foreach ($danhSachSanPham as $product)
                            <a href="{{ route('user.productDetails', $product->id) }}" class="book-card text-center text-dark text-decoration-none">
                                <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover" alt="{{ $product->name }}">
                                <h3 class="book-title" title="{{ $product->name }}">{{ $product->name }}</h3>
                                <p class="book-price">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
                            </a>
                        @endforeach
                    </div>
                    
                    <!-- Hiển thị phân trang -->
                    <div class="mt-5 d-flex justify-content-center custom-pagination">
                        {{ $danhSachSanPham->links() }}
                    </div>
                @endif
            @endif

        </section>
    </div>
</div>

<!-- Tùy chỉnh nhẹ CSS cho phần Phân trang (Pagination của Laravel) -->
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