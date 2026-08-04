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
                <a href="{{ route('user.shop') }}" class="btn btn-orange rounded-pill px-4 font-weight-bold" style="background-color: var(--primary-color); color: #fff;">Đến cửa hàng ngay</a>
            </div>
        @else
            <!-- Lưới sách -->
            <div class="book-grid">
                @foreach($wishlists as $product)
                    <div class="book-card text-center position-relative d-flex flex-column shadow-sm border h-100" style="border-radius: 12px; overflow: hidden; background: #fff;">
                        
                        <!-- KIỂM TRA SẢN PHẨM CÓ CÒN TỒN TẠI HAY KHÔNG -->
                        @if($product->product_id)
                            <!-- 1. SẢN PHẨM BÌNH THƯỜNG -->
                            <a href="{{ route('user.productDetails', $product->product_id) }}" class="text-decoration-none text-dark d-block flex-grow-1 p-3">
                                <img src="{{ asset('uploads/products/' . $product->image) }}" class="book-cover mb-3" style="height: 200px; object-fit: cover; border-radius: 8px;" alt="{{ $product->name }}">
                                <h3 class="book-title mb-2" title="{{ $product->name }}" style="font-size: 16px; font-weight: 600; height: 45px; overflow: hidden;">{{ $product->name }}</h3>
                                <p class="book-price font-weight-bold mb-0" style="color: #D35400;">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
                            </a>

                            <!-- 2 NÚT HÀNH ĐỘNG (Dùng class btn-remove-wishlist mới) -->
                            <div class="d-flex px-3 pb-3 mt-auto">
                                <button class="btn btn-outline-danger btn-sm btn-remove-wishlist flex-fill mr-2 font-weight-bold" data-id="{{ $product->original_product_id }}" title="Gỡ khỏi danh sách">
                                    <i class="fas fa-heart-broken mr-1"></i> Bỏ thích
                                </button>
                                <a href="{{ route('user.productDetails', $product->product_id) }}" class="btn btn-primary btn-sm flex-fill font-weight-bold" style="background-color: var(--primary-color); border: none;">
                                    <i class="fas fa-shopping-cart mr-1"></i> Mua ngay
                                </a>
                            </div>

                        @else
                            <!-- 2. SẢN PHẨM ĐÃ BỊ XÓA (NGỪNG KINH DOANH) -->
                            <div class="p-3 position-relative flex-grow-1 d-flex flex-column" style="opacity: 0.6; filter: grayscale(100%); pointer-events: none;">
                                <span class="badge badge-secondary position-absolute shadow-sm" style="top: 15px; left: 15px; z-index: 10; font-size: 12px;">Ngừng kinh doanh</span>
                                
                                <div class="d-flex justify-content-center align-items-center mb-3 w-100" style="height: 200px; background-color: #f1f3f5; border-radius: 8px; border: 1px dashed #ced4da;">
                                    <i class="fas fa-box-open fa-3x text-muted" style="opacity: 0.5;"></i>
                                </div>
                                
                                <h3 class="book-title mb-2 text-muted" style="font-size: 16px; font-weight: 600;">Sản phẩm không còn tồn tại</h3>
                                <p class="book-price text-muted mb-0">---</p>
                            </div>

                            <!-- NÚT GỠ BỎ -->
                            <div class="px-3 pb-3 mt-auto">
                                <button class="btn btn-secondary btn-sm btn-block btn-remove-wishlist font-weight-bold" data-id="{{ $product->original_product_id }}" style="pointer-events: auto;">
                                    <i class="fas fa-trash-alt mr-1"></i> Gỡ khỏi danh sách
                                </button>
                            </div>
                        @endif

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
// Dùng Javascript thuần tương tự file index để tránh mọi lỗi xung đột thư viện
document.addEventListener('DOMContentLoaded', function () {
    toastr.options = { 
        "closeButton": true, 
        "progressBar": true, 
        "positionClass": "toast-bottom-right", 
        "timeOut": "2500" 
    };

    // Truy vấn tất cả các nút có class btn-remove-wishlist
    const removeButtons = document.querySelectorAll('.btn-remove-wishlist');

    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation();

            let btn = this;
            let productId = btn.getAttribute('data-id');

            if (!productId) {
                toastr.error("Lỗi: Không tìm thấy ID sản phẩm.");
                return;
            }

            // Chặn spam click liên tục (khóa nút)
            if (btn.classList.contains('is-loading')) return;
            btn.classList.add('is-loading');
            
            // Đổi giao diện nút thành đang tải
            let originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Đang xử lý...';
            btn.style.opacity = '0.6';
            btn.style.pointerEvents = 'none';

            // Xử lý Xóa bằng AJAX
            axios.post('{{ route("user.wishlist.toggle") }}', {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            })
            .then(function (response) {
                if(response.data.status === 'removed') {
                    toastr.info("Đã gỡ khỏi danh sách yêu thích!");
                    
                    // Tạo hiệu ứng mờ dần (Vanilla JS) rồi xóa phần tử HTML
                    let card = btn.closest('.book-card');
                    card.style.transition = "opacity 0.3s ease";
                    card.style.opacity = "0";
                    
                    setTimeout(() => {
                        card.remove();
                        // Nếu xóa hết sạch sách trên màn hình thì reload lại để hiện bảng "Trống"
                        if(document.querySelectorAll('.book-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                } else {
                    // Mở khóa nếu lỡ trả về trạng thái khác
                    btn.classList.remove('is-loading');
                    btn.innerHTML = originalHtml;
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                }
            })
            .catch(function (error) {
                console.error("Lỗi xóa wishlist:", error);
                toastr.error("Có lỗi xảy ra, vui lòng thử lại!");
                // Mở khóa nút
                btn.classList.remove('is-loading');
                btn.innerHTML = originalHtml;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            });
        });
    });
});
</script>
@endpush