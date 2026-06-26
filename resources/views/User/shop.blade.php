@include('User.header')

{{-- Breadcrumb --}}
<section style="background: #f6f6f6; padding: 14px 0; border-bottom: 1px solid #e9e9e9;">
    <div class="container">
        <ol class="breadcrumb" style="background:transparent; padding:0; margin:0; font-size:13px;">
            <li class="breadcrumb-item">
                <a href="{{ route('user.index') }}" style="color:#7fad39;">Trang chủ</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('user.shop') }}" style="color:#7fad39;">Cửa hàng</a>
            </li>
            @isset($danhMuc)
                <li class="breadcrumb-item active" style="color:#6c757d;">{{ $danhMuc->name }}</li>
            @endisset
        </ol>
    </div>
</section>

<section class="featured spad">
    <div class="container">
        <div class="row">

            {{-- ===== SIDEBAR ===== --}}
            <div class="col-lg-3 col-md-4 mb-4">

                {{-- Bộ lọc (chỉ hiện khi đang ở trang danh mục) --}}
                @isset($danhMuc)
                <div class="card border-0 shadow-sm mb-4"
                     style="border-radius:6px; border:1px solid #ececec !important; overflow:hidden;">
                    <div class="card-header text-white font-weight-bold"
                         style="background:#7fad39; padding:12px 16px; font-size:13px;">
                        <i class="fa fa-filter mr-1"></i> BỘ LỌC TÌM KIẾM
                    </div>
                    <div class="card-body" style="padding:18px 16px;">
                        <form action="{{ route('user.category', $danhMuc->id) }}" method="GET">

                            {{-- Giá --}}
                            <p class="filter-label">Khoảng Giá (VND)</p>
                            <div class="d-flex align-items-center mb-3" style="gap:6px;">
                                <input type="number" name="price_min"
                                       value="{{ request('price_min') }}"
                                       placeholder="Từ"
                                       class="form-control form-control-sm text-center"
                                       style="border-radius:4px; font-size:13px;">
                                <span class="text-muted">–</span>
                                <input type="number" name="price_max"
                                       value="{{ request('price_max') }}"
                                       placeholder="Đến"
                                       class="form-control form-control-sm text-center"
                                       style="border-radius:4px; font-size:13px;">
                            </div>

                            <hr style="border-top:1px dashed #ddd; margin:12px 0;">

                            {{-- Tác giả --}}
                            <p class="filter-label">Tác Giả</p>
                            <select name="author" class="form-control form-control-sm mb-3"
                                    style="border-radius:4px; font-size:13px; cursor:pointer;">
                                <option value="">-- Chọn Tác Giả --</option>
                                @foreach($tacGia as $tg)
                                    <option value="{{ $tg->id }}"
                                        {{ request('author') == $tg->id ? 'selected' : '' }}>
                                        {{ $tg->name }}
                                    </option>
                                @endforeach
                            </select>

                            <hr style="border-top:1px dashed #ddd; margin:12px 0;">

                            {{-- NXB --}}
                            <p class="filter-label">Nhà Xuất Bản</p>
                            <select name="publisher" class="form-control form-control-sm mb-3"
                                    style="border-radius:4px; font-size:13px; cursor:pointer;">
                                <option value="">-- Chọn NXB --</option>
                                @foreach($nhaXuatBan as $nxb)
                                    <option value="{{ $nxb->id }}"
                                        {{ request('publisher') == $nxb->id ? 'selected' : '' }}>
                                        {{ $nxb->name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Nút áp dụng --}}
                            <button type="submit"
                                    class="btn btn-block text-white font-weight-bold btn-sm"
                                    style="background:#7fad39; border:none; border-radius:4px;
                                           padding:9px; font-size:13px; letter-spacing:.4px;">
                                <i class="fa fa-check mr-1"></i> ÁP DỤNG
                            </button>

                            @if(request()->hasAny(['price_min','price_max','author','publisher']))
                                <a href="{{ route('user.category', $danhMuc->id) }}"
                                   class="btn btn-secondary btn-block btn-sm font-weight-bold mt-2"
                                   style="border-radius:4px; padding:8px; font-size:13px;">
                                    <i class="fa fa-times mr-1"></i> XÓA BỘ LỌC
                                </a>
                            @endif

                        </form>
                    </div>
                </div>
                @endisset

                {{-- Danh sách danh mục --}}
                <div class="hero__categories">
                    <div class="hero__categories__all">
                        <i class="fa fa-bars"></i>
                        <span>TẤT CẢ DANH MỤC</span>
                    </div>
                    <ul>
                        @foreach ($tatCaDanhMuc as $dm)
                            <li>
                                <a href="{{ route('user.category', $dm->id) }}"
                                   style="{{ isset($danhMuc) && $dm->id == $danhMuc->id
                                             ? 'color:#7fad39; font-weight:bold;' : '' }}">
                                    {{ $dm->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- ===== NỘI DUNG CHÍNH ===== --}}
            <div class="col-lg-9 col-md-8">

                {{-- ── Trang danh mục (có lọc / phân trang) ── --}}
                @isset($danhMuc)

                    {{-- Tiêu đề + sắp xếp --}}
                    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap"
                         style="border-bottom:2px solid #7fad39; padding-bottom:10px; gap:10px;">
                        <h4 style="margin:0; font-size:20px; font-weight:700; color:#252525;">
                            {{ $danhMuc->name }}
                            <span style="font-size:14px; font-weight:400; color:#6c757d; margin-left:6px;">
                                ({{ $danhSachSanPham->total() }} sản phẩm)
                            </span>
                        </h4>

                        <select onchange="window.location=this.value"
                                class="form-control form-control-sm"
                                style="width:auto; font-size:13px; border-radius:4px;
                                       border:1px solid #ced4da; cursor:pointer;">
                            <option value="{{ request()->fullUrlWithQuery(['sort'=>'']) }}">Mặc định</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort'=>'price_asc']) }}"
                                {{ request('sort')=='price_asc'  ? 'selected':'' }}>Giá tăng dần</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort'=>'price_desc']) }}"
                                {{ request('sort')=='price_desc' ? 'selected':'' }}>Giá giảm dần</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort'=>'newest']) }}"
                                {{ request('sort')=='newest'     ? 'selected':'' }}>Mới nhất</option>
                        </select>
                    </div>

                    {{-- Lưới sản phẩm --}}
                    <div class="row">
                        @if($danhSachSanPham->isEmpty())
                            <div class="col-12 text-center" style="padding:60px 0;">
                                <i class="fa fa-book" style="font-size:48px; color:#ddd; display:block; margin-bottom:15px;"></i>
                                <h5 class="text-muted">Không tìm thấy sản phẩm nào!</h5>
                                <a href="{{ route('user.category', $danhMuc->id) }}"
                                   class="primary-btn" style="display:inline-block; margin-top:15px;">
                                   Xóa bộ lọc
                                </a>
                            </div>
                        @else
                            @foreach ($danhSachSanPham as $sp)
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <a href="{{ route('user.productDetails', $sp->id) }}"
                                       style="display:block; text-decoration:none; color:inherit;">
                                        <div class="featured__item">
                                            <div class="featured__item__pic set-bg"
                                                 data-setbg="{{ asset('uploads/products/'.$sp->image) }}">
                                            </div>
                                            <div class="featured__item__text">
                                                <h6>{{ $sp->name }}</h6>
                                                <h5>{{ number_format($sp->price,0,',','.') }} VND</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Phân trang --}}
                    @if($danhSachSanPham->hasPages())
                        <div class="d-flex justify-content-center mt-3 mb-2">
                            <nav>
                                <ul class="pagination pagination-shop">
                                    {{-- Nút Previous --}}
                                    @if($danhSachSanPham->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">‹</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $danhSachSanPham->appends(request()->query())->previousPageUrl() }}">‹</a>
                                        </li>
                                    @endif

                                    {{-- Các số trang --}}
                                    @php
                                        $currentPage  = $danhSachSanPham->currentPage();
                                        $lastPage     = $danhSachSanPham->lastPage();
                                        $start        = max(1, $currentPage - 2);
                                        $end          = min($lastPage, $currentPage + 2);
                                    @endphp

                                    @if($start > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $danhSachSanPham->appends(request()->query())->url(1) }}">1</a>
                                        </li>
                                        @if($start > 2)
                                            <li class="page-item disabled"><span class="page-link">…</span></li>
                                        @endif
                                    @endif

                                    @for($i = $start; $i <= $end; $i++)
                                        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $danhSachSanPham->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    @if($end < $lastPage)
                                        @if($end < $lastPage - 1)
                                            <li class="page-item disabled"><span class="page-link">…</span></li>
                                        @endif
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $danhSachSanPham->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                                        </li>
                                    @endif

                                    {{-- Nút Next --}}
                                    @if($danhSachSanPham->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $danhSachSanPham->appends(request()->query())->nextPageUrl() }}">›</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">›</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif

                {{-- ── Trang shop tổng (theo từng danh mục) ── --}}
                @else

                    @foreach ($sanPhamTheoDanhMuc as $dm)
                        {{-- Tiêu đề danh mục --}}
                        <div class="d-flex align-items-center justify-content-between mb-3"
                             style="border-bottom:2px solid #7fad39; padding-bottom:8px;">
                            <h5 style="margin:0; font-weight:700; color:#252525; font-size:17px;">
                                {{ $dm->name }}
                            </h5>
                            <a href="{{ route('user.category', $dm->id) }}"
                               style="font-size:13px; color:#7fad39; white-space:nowrap;">
                                Xem tất cả <i class="fa fa-angle-right"></i>
                            </a>
                        </div>

                        {{-- 6 sản phẩm đầu --}}
                        <div class="row mb-5">
                            @foreach ($dm->sanPham as $sp)
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <a href="{{ route('user.productDetails', $sp->id) }}"
                                       style="display:block; text-decoration:none; color:inherit;">
                                        <div class="featured__item">
                                            <div class="featured__item__pic set-bg"
                                                 data-setbg="{{ asset('uploads/products/'.$sp->image) }}">
                                            </div>
                                            <div class="featured__item__text">
                                                <h6>{{ $sp->name }}</h6>
                                                <h5>{{ number_format($sp->price,0,',','.') }} VND</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                @endisset

            </div>{{-- /col-lg-9 --}}
        </div>
    </div>
</section>

<style>
/* ===== FILTER LABEL ===== */
.filter-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #6c757d;
    margin-bottom: 8px;
}

/* ===== PAGINATION FIX ===== */
.pagination-shop {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination-shop .page-item .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    font-size: 14px;
    font-weight: 500;
    color: #7fad39;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-decoration: none;
    transition: background .2s, color .2s, border-color .2s;
    line-height: 1;
}

.pagination-shop .page-item .page-link:hover {
    background: #7fad39;
    color: #fff;
    border-color: #7fad39;
}

.pagination-shop .page-item.active .page-link {
    background: #7fad39;
    color: #fff;
    border-color: #7fad39;
    pointer-events: none;
}

.pagination-shop .page-item.disabled .page-link {
    color: #adb5bd;
    pointer-events: none;
    background: #f8f9fa;
    border-color: #dee2e6;
}
</style>

@include('User.footer')