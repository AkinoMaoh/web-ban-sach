@extends('layout.user')

@section('content')

<!-- 1. Hero Banner Slider (Khôi phục Banner Trượt) -->
<section class="container mt-4 mb-5">
    <div id="heroCarousel" class="carousel slide shadow-sm" data-ride="carousel" style="border-radius: 12px; overflow: hidden;">
        <div class="carousel-inner" style="background: #2C3E50;">
            @foreach($bannerBooks as $book)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <div class="row align-items-center p-5">
                        <div class="col-md-7 text-white pl-md-5">
                            <span class="badge badge-warning mb-3 px-3 py-2">SÁCH NỔI BẬT</span>
                            <h1 class="serif-font display-4 mb-3" style="font-weight: 700;">{{ $book->name }}</h1>
                            <p class="mb-4" style="font-size: 18px; color: #BDC3C7;">Hãy khám phá tác phẩm đang được yêu thích nhất tuần này.</p>
                            <h3 class="mb-4" style="color: #E67E22;">{{ number_format($book->price, 0, ',', '.') }} VNĐ</h3>
                            <a href="{{ route('user.productDetails', $book->id) }}" class="btn text-white rounded-pill px-4 py-2" style="background: #D35400; font-weight: 600;">Xem chi tiết <i class="fas fa-arrow-right ml-2"></i></a>
                        </div>
                        <div class="col-md-5 text-center d-none d-md-block">
                            <img src="{{ asset('uploads/products/'.$book->image) }}" class="shadow-lg rounded" style="max-height: 380px; transform: rotate(5deg);">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
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

<!-- 3. Sách nổi bật / Gợi ý -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
        <h2 class="serif-font font-weight-bold mb-0">Tác phẩm nổi bật</h2>
        <a href="{{ route('user.shop') }}" class="text-muted text-decoration-none">Xem tất cả <i class="fas fa-angle-right"></i></a>
    </div>
    
    @if($products->isEmpty())
        <div class="text-center text-muted py-5">Không tìm thấy sách nào.</div>
    @else
        <div class="book-grid">
            @foreach ($products as $product)
                <a href="{{ route('user.productDetails', $product->id) }}" class="text-decoration-none text-dark">
                    <div class="book-card text-center">
                        <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover" alt="{{ $product->name }}">
                        <h3 class="book-title" title="{{ $product->name }}">{{ $product->name }}</h3>
                        <p class="book-price">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

<!-- 4. Banner quảng cáo tĩnh -->
<!-- <section class="container mb-5 mt-5">
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="rounded shadow-sm overflow-hidden" style="height: 250px; background: url('img/banner/banner-1.jpg') center/cover;"></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="rounded shadow-sm overflow-hidden" style="height: 250px; background: url('img/banner/banner-2.jpg') center/cover;"></div>
        </div>
    </div>
</section> -->

<!-- 5. Top 5 Sách mới & Top 5 Bán chạy -->
<!-- 5. Top 5 Sách mới & Top 5 Bán chạy (Dạng Slider) -->
<section class="container mb-5 pt-4">
    <div class="row">
        <!-- Cột Top 5 Mới Nhất -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h3 class="serif-font font-weight-bold mb-0"><i class="fas fa-clock mr-2" style="color: #D35400;"></i> Sách mới nhất</h3>
                <!-- Nút điều hướng Slider -->
                <div>
                    <a href="#newBooksCarousel" role="button" data-slide="prev" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="#newBooksCarousel" role="button" data-slide="next" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <div id="newBooksCarousel" class="carousel slide" data-ride="carousel" data-interval="3500">
                <div class="carousel-inner bg-light p-4 rounded shadow-sm border" style="min-height: 240px;">
                    @foreach ($product5 as $index => $pro)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <a href="{{ route('user.productDetails', $pro->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                <!-- Ảnh to hơn vì đứng 1 mình 1 slide -->
                                <img src="{{ asset('uploads/products/' . $pro->image) }}" class="rounded shadow" style="width: 120px; height: 180px; object-fit: cover;">
                                <div class="ml-4">
                                    <span class="badge mb-2" style="background-color: #2C3E50; color: white;">MỚI PHÁT HÀNH</span>
                                    <h5 class="font-weight-bold mb-2" style="font-size: 18px; line-height: 1.4;">{{ $pro->name }}</h5>
                                    <h4 class="mb-3" style="color: #D35400; font-weight: 700;">{{ number_format($pro->price, 0, ',', '.') }} ₫</h4>
                                    <span class="btn btn-outline-dark btn-sm rounded-pill px-4">Đọc ngay</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cột Top 5 Bán Chạy -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h3 class="serif-font font-weight-bold mb-0"><i class="fas fa-fire mr-2" style="color: #e74c3c;"></i> Sách bán chạy</h3>
                <!-- Nút điều hướng Slider -->
                <div>
                    <a href="#bestSellingCarousel" role="button" data-slide="prev" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="#bestSellingCarousel" role="button" data-slide="next" class="btn btn-sm btn-light rounded-circle shadow-sm mx-1 text-muted">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <div id="bestSellingCarousel" class="carousel slide" data-ride="carousel" data-interval="4000">
                <div class="carousel-inner bg-light p-4 rounded shadow-sm border" style="min-height: 240px;">
                    @foreach ($topSanPham as $index => $pro)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <a href="{{ route('user.productDetails', $pro->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                <!-- Ảnh to hơn vì đứng 1 mình 1 slide -->
                                <img src="{{ asset('uploads/products/' . $pro->image) }}" class="rounded shadow" style="width: 120px; height: 180px; object-fit: cover;">
                                <div class="ml-4">
                                    <span class="badge mb-2" style="background-color: #e74c3c; color: white;">BEST SELLER</span>
                                    <h5 class="font-weight-bold mb-2" style="font-size: 18px; line-height: 1.4;">{{ $pro->name }}</h5>
                                    <h4 class="mb-3" style="color: #D35400; font-weight: 700;">{{ number_format($pro->price, 0, ',', '.') }} ₫</h4>
                                    <span class="btn btn-outline-dark btn-sm rounded-pill px-4">Đọc ngay</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
