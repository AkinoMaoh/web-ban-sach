@extends('layout.user')

@section('content')

<!-- 1. Hero Banner Slider -->
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

<!-- 3. Tác phẩm nổi bật -->
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
                <div class="book-card text-center position-relative">
                    <!-- Nút Wishlist -->
                    <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $product->id }}" style="top: 10px; right: 10px; z-index: 10; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: none;">
                        <i class="far fa-heart" style="color: #D35400; font-size: 16px;"></i>
                    </button>

                    <a href="{{ route('user.productDetails', $product->id) }}" class="text-decoration-none text-dark d-block">
                        <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover" alt="{{ $product->name }}">
                        <h3 class="book-title" title="{{ $product->name }}">{{ $product->name }}</h3>
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
                                <button class="btn btn-white rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $pro->id }}" style="top: 0; right: 0; z-index: 10; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: none; background: white;">
                                    <i class="far fa-heart" style="color: #D35400; font-size: 16px;"></i>
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
                                <button class="btn btn-white rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $pro->id }}" style="top: 0; right: 0; z-index: 10; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: none; background: white;">
                                    <i class="far fa-heart" style="color: #D35400; font-size: 16px;"></i>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function(){
    toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right", "timeOut": "2500" };

    $('.btn-wishlist').click(function(e) {
        e.preventDefault(); 
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
            if(error.response && error.response.status === 401) {
                toastr.warning("Đăng nhập để thêm vào danh sách yêu thích!");
            } else {
                toastr.error("Có lỗi xảy ra!");
            }
        });
    });
});
</script>
@endpush