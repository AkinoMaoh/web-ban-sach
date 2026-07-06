@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Sách yêu thích</li>
            </ol>
        </nav>
    </div>
</div>

<section class="wishlist-section mb-5 pb-5">
    <div class="container">
        <h2 class="serif-font font-weight-bold mb-4" style="color: #2C3E50;">Sách Yêu Thích Của Tôi</h2>

        @if($wishlists->isEmpty())
            <div class="text-center py-5 bg-white shadow-sm border" style="border-radius: 12px;">
                <i class="far fa-heart fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                <h5 class="serif-font text-muted mb-3">Bạn chưa có cuốn sách yêu thích nào.</h5>
                <a href="{{ route('user.shop') }}" class="btn btn-orange rounded-pill px-4 font-weight-bold">Đến cửa hàng ngay</a>
            </div>
        @else
            <!-- Lưới sách (Đồng bộ với Index) -->
            <div class="book-grid">
                @foreach($wishlists as $product)
                    <div class="book-card text-center position-relative">
                        <!-- Nút Wishlist (Tương tác AJAX) -->
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist position-absolute" data-id="{{ $product->product_id }}" style="top: 10px; right: 10px; z-index: 10; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: none;">
                            <i class="fas fa-heart" style="color: #D35400; font-size: 16px;"></i>
                        </button>

                        <a href="{{ route('user.productDetails', $product->product_id) }}" class="text-decoration-none text-dark d-block">
                            <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover" alt="{{ $product->name }}">
                            <h3 class="book-title" title="{{ $product->name }}">{{ $product->name }}</h3>
                            <p class="book-price">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-5">
                {{ $wishlists->links() }}
            </div>
        @endif
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
            // Khi ở trang Wishlist, nếu bỏ tim thì xóa luôn cái thẻ đó khỏi màn hình cho đẹp
            if(response.data.status === 'removed') {
                btn.closest('.book-card').fadeOut(300, function() { $(this).remove(); });
                toastr.info("Đã gỡ khỏi danh sách yêu thích!");
            } else {
                icon.removeClass('far').addClass('fas');
                toastr.success(response.data.message);
            }
        })
        .catch(function (error) {
            toastr.error("Đăng nhập để thêm vào danh sách yêu thích!");
        });
    });
});
</script>
@endpush