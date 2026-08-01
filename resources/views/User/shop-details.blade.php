@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.shop') }}" class="text-muted">Tủ sách</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="product-details spad mb-5 pb-5">
    <div class="container">
        <!-- Thông báo -->
        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0" style="border-left: 5px solid #dc3545; border-radius: 6px;"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0" style="border-left: 5px solid #28a745; border-radius: 6px;"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</div>
        @endif

        <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
            <form id="them-vao-gio-hang" action="{{ route('cart.add') }}" method="POST">
                @csrf
                <div class="row">
                   <!-- Ảnh Sách -->
<div class="col-lg-5 mb-4 mb-lg-0 text-center position-relative">

    {{-- Bóng giảm giá --}}
    <span id="discount-ball"
        class="position-absolute align-items-center justify-content-center text-white"
        style="
            display:none;
            top:10px;
            left:10px;
            width:45px;
            height:45px;
            border-radius:50%;
            background:#D35400;
            font-size:13px;
            font-weight:700;
            z-index:20;
        ">
    </span>


    {{-- Ảnh chính --}}
    <div class="main-image mb-3 w-100">
        <img id="main-product-image"
             src="{{ asset('uploads/products/' . $product->image) }}"
             class="img-fluid rounded shadow"
             style="
                width:100%;
                height:520px;
                object-fit:contain;
             "
             alt="{{ $product->name }}">
    </div>


    {{-- Danh sách ảnh nhỏ --}}
{{-- Danh sách ảnh nhỏ --}}
<div class="thumbnail-wrapper position-relative">

    <button type="button" class="thumb-btn prev" onclick="prevThumbnail()">
        ‹
    </button>


    <div class="thumbnail-view">

        <div class="thumbnail-list" id="thumbnailList">

            @foreach($product->images->sortBy('sort_order') as $image)

                <div class="thumbnail-item">

                    <img src="{{ asset('uploads/products/'.$image->image) }}"
                         class="product-thumbnail"
                         onclick="changeImage(this.src)">

                </div>

            @endforeach

        </div>

    </div>


    <button type="button" class="thumb-btn next" onclick="nextThumbnail()">
        ›
    </button>

</div>

</div>
                 
                    
                    <!-- Thông tin Sách -->
                    <div class="col-lg-7 pl-lg-5">
                        <h1 class="serif-font font-weight-bold mb-3" style="color: var(--text-main); line-height: 1.3;">{{ $product->name }}</h1>
                        
                        @php
                            $variant = $product->variants->where('stock', '>', 0)->first() ?? $product->variants->first();
                        @endphp

                        <h2 class="display-4 font-weight-bold mb-4">
                            <div id="price-box">
                                <div class="d-flex align-items-center flex-wrap">
                                    <span id="sale-price" style="font-size:34px;font-weight:700;">
                                        {{ number_format(($variant && $variant->sale_price > 0 && $variant->sale_price < $variant->price) ? $variant->sale_price : ($variant?->price ?? $product->price), 0, ',', '.') }} ₫
                                    </span>
                                    <span id="old-price" class="ml-3" style="color:#999;font-size:24px;text-decoration:line-through; {{ ($variant && $variant->sale_price > 0 && $variant->sale_price < $variant->price) ? '' : 'display:none;' }}">
                                        {{ number_format($variant?->price ?? 0, 0, ',', '.') }} ₫
                                    </span>
                                </div>
                            </div>
                        </h2>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">Tác giả: {{ $product->author->name }}</p>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">NXB: {{ $product->publishers->name }}</p>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">Danh mục: {{ $product->category->name }}</p>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">Mô tả: {{ $product->description }}</p>

                        <!-- Box Chọn Phiên Bản -->
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="serif-font font-weight-bold mb-3">Chọn phiên bản:</h5>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @php $hasChecked = false; @endphp
                                @foreach($product->variants as $bienThe)
                                    @if($bienThe->price > 0)
                                        <label class="chon-phien-ban mb-2 mr-2">
                                            <input type="radio"
                                                name="product_variant_id"
                                                value="{{ $bienThe->id }}"
                                                data-price="{{ $bienThe->price }}"
                                                data-sale-price="{{ $bienThe->sale_price ?? 0 }}"
                                                data-discount="{{ $bienThe->discount_percent }}"
                                                data-stock="{{ $bienThe->stock }}"
                                                {{ $bienThe->stock <= 0 ? 'disabled' : '' }}
                                                @if(!$hasChecked && $bienThe->stock > 0)
                                                    checked
                                                    @php $hasChecked = true; @endphp
                                                @endif
                                                required>
                                            <span class="hop-phien-ban">
                                                <strong class="d-block mb-1 text-dark">{{ $bienThe->edition }}</strong>
                                                <small class="d-block mb-1">
                                                    @if($bienThe->sale_price > 0 && $bienThe->sale_price < $bienThe->price)
                                                        <span class="text-danger font-weight-bold">{{ number_format($bienThe->sale_price) }} VNĐ</span>
                                                    @else
                                                        <span class="text-muted">{{ number_format($bienThe->price) }} VNĐ</span>
                                                    @endif
                                                </small>
                                                <small class="{{ $bienThe->stock > 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                                    {{ $bienThe->stock > 0 ? 'Còn ' . $bienThe->stock : 'Hết hàng' }}
                                                </small>
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Chọn Số Lượng -->
                        <div class="mb-4">
                            <h5 class="serif-font font-weight-bold mb-3">Số lượng :</h5>
                            <div class="input-group" style="width: 140px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary font-weight-bold" type="button" onclick="let q=document.getElementById('o-so-luong'); if(q.value>1)q.value--; q.dispatchEvent(new Event('input'))">-</button>
                                </div>
                                <input type="number" name="quantity" id="o-so-luong" class="form-control text-center font-weight-bold" value="1" min="1">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary font-weight-bold" type="button" onclick="let q=document.getElementById('o-so-luong'); q.value++; q.dispatchEvent(new Event('input'))">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Nút Hành Động -->
                        <div class="d-flex align-items-center mt-4 pt-3 border-top">
                            <button type="submit" name="action_type" value="add_to_cart" class="btn btn-dark rounded-pill px-4 py-3 font-weight-bold mr-2 shadow-sm">
                                <i class="fas fa-cart-plus mr-2"></i> Thêm vào giỏ
                            </button>
                            <button type="submit" name="action_type" value="buy_now" class="btn btn-orange rounded-pill px-4 py-3 font-weight-bold shadow-sm mr-2" style="background-color: var(--primary-color); color: #fff;">
                                <i class="fas fa-bolt mr-2"></i> Mua ngay
                            </button>
                            <button type="button" class="btn btn-outline-danger rounded-circle shadow-sm btn-wishlist" data-id="{{ $product->id }}" style="width: 55px; height: 55px;" title="Thêm vào yêu thích">
                                <i class="far fa-heart" style="font-size: 20px;"></i>
                            </button>
                        </div>   
                    </div>
                </div>
            </form>
        </div>

        {{-- Đánh giá & Nhận xét --}}
        <div id="review-section" class="card p-3 p-md-4 border-0 shadow-sm rounded-3 mt-4">
            <h5 class="serif-font font-weight-bold mb-1">Đánh giá & Nhận xét</h5>
            <div class="border-top pt-4 mt-3">  
                <div class="row align-items-center mb-4">
                    <!-- Điểm số trung bình -->
                    <div class="col-md-3 col-lg-2 text-center border-end py-2">
                        <h2 class="fw-bold text-dark mb-0">{{ $avgRating ?? 0 }}<span class="fs-6 text-muted">/5</span></h2>
                        <div class="text-warning fs-6 my-1">
                            @php $roundedAvg = round($avgRating ?? 0); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $roundedAvg) <i class="fas fa-star"></i> @else <i class="far fa-star"></i> @endif
                            @endfor
                        </div>
                        <p class="text-muted small mb-0" style="font-size: 0.85rem;">({{ $totalReviews ?? 0 }} đánh giá)</p>
                    </div>

                    <!-- Đồ thị phần trăm -->
                    <div class="col-md-4 col-lg-3 px-3 border-end py-2">
                        @for ($i = 5; $i >= 1; $i--)
                        <div class="d-flex align-items-center mb-1" style="font-size: 0.85rem;">
                            <span style="min-width: 40px;" class="text-muted">{{ $i }} sao</span>
                            <div class="progress flex-grow-1 mx-2" style="height: 6px; background-color: #f0f2f5;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $ratingPercentages[$i] ?? 0 }}%"></div>
                            </div>
                            <span style="min-width: 45px;" class="text-end text-muted">{{ $ratingPercentages[$i] ?? 0 }}%</span>
                        </div>
                        @endfor
                    </div>

                    <!-- Form đánh giá -->
                    <div class="col-md-5 col-lg-7 ps-md-4 text-start py-2">
                        @guest
                            <div class="bg-light p-3 rounded text-center">
                                <p class="text-muted mb-2">Chỉ có thành viên mới có thể viết nhận xét.</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Đăng nhập</a>
                            </div>
                        @else
                            @php 
                                $hasBought = Auth::user()->hasBoughtProduct($product->id);
                                $unreviewedDetails = Auth::user()->getUnreviewedOrderDetails($product->id);
                            @endphp

                            @if(!$hasBought)
                                <div class="bg-light p-4 rounded text-center shadow-sm border border-white">
                                    <p class="text-muted mb-0 font-weight-bold">Bạn chưa mua sản phẩm này</p>
                                </div>
                            @elseif($unreviewedDetails->count() > 0)
                                <form action="{{ route('review.store') }}" method="POST" id="form-danh-gia">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <h6 class="font-weight-bold mb-2">Gửi đánh giá của bạn</h6>
                                    <select name="order_detail_id" class="form-control mb-2" required>
                                        <option value="">-- Chọn đơn hàng để đánh giá --</option>
                                        @foreach($unreviewedDetails as $detail)
                                            <option value="{{ $detail->id }}">Đơn #{{ $detail->order_id }} - {{ $detail->variant->edition ?? 'Mặc định' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="star-rating mb-2">
                                        <input type="radio" id="star5" name="rating" value="5"><label for="star5"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4"><label for="star4"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3"><label for="star3"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2"><label for="star2"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1"><label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                    <textarea class="form-control mb-2 shadow-sm" name="comment" rows="2" placeholder="Nhận xét của bạn..." required style="resize: none;"></textarea>
                                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4">Gửi nhận xét</button>
                                </form>
                            @else
                                <div class="bg-light p-4 rounded text-center shadow-sm border border-white">
                                    <p class="text-muted mb-0 font-weight-bold">Bạn đã đánh giá tất cả đơn hàng</p>
                                </div>
                            @endif
                        @endguest
                    </div>
                </div>

                <!-- Danh sách bình luận -->
                <div class="comments-list mt-4 pt-3 border-top">
                    @forelse($product->reviews as $review)
                        <!-- GẮN ID CHÍNH XÁC CHO TỪNG REVIEW ĐỂ HỆ THỐNG NHẬN DIỆN -->
                        <div class="d-flex mb-4 p-2 rounded review-item" id="review-{{ $review->id }}">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? $review->user_name) }}&background=random" 
                                 alt="Avatar" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit: cover;">
                            
                            <div class="ms-3 pl-3 w-100">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <h6 class="font-weight-bold mb-0 text-dark mr-2">{{ $review->user->name ?? $review->user_name }}</h6>
                                        @if($review->is_buyer)
                                            <span class="badge text-white rounded-pill d-flex align-items-center mr-2" style="font-size: 0.65rem; padding: 0.3em 0.6em; background-color: #28a745;">
                                                <i class="fas fa-check-circle mr-1"></i> Đã mua hàng
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0 mr-3 btn-like-review" data-id="{{ $review->id }}" style="text-decoration: none; box-shadow: none;">
                                            <i class="{{ $review->isLikedByAuthUser() ? 'fas text-primary' : 'far' }} fa-thumbs-up icon-like"></i>
                                            <span class="text-like {{ $review->isLikedByAuthUser() ? 'text-primary font-weight-bold' : '' }}">Hữu ích</span>
                                            (<span class="like-count">{{ $review->likesCount() }}</span>)
                                        </button>
                                        <small class="text-muted ml-2 text-nowrap">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                
                                <div class="text-warning mb-2" style="font-size: 0.85rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating) <i class="fas fa-star"></i> @else <i class="far fa-star"></i> @endif
                                    @endfor
                                </div>
                                
                                <p class="text-muted mb-0" style="font-size: 0.95rem;">{{ $review->comment }}</p>

                                @if($review->admin_reply)
                                <div class="admin-reply-box mt-3 p-3 rounded shadow-sm bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="admin-reply-icon text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm mr-2 bg-primary" style="width: 30px; height: 30px;">
                                            <i class="fas fa-headset" style="font-size: 14px;"></i>
                                        </div>
                                        <div class="admin-reply-title">
                                            <h6 class="mb-0 font-weight-bold text-primary" style="font-size: 14px;">Phản hồi từ Shop</h6>
                                        </div>
                                    </div>
                                    <div class="admin-reply-content ml-4 pl-1" style="font-size: 14px;">
                                        {!! nl2br(e($review->admin_reply)) !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <hr class="text-muted" style="opacity: 0.1">
                    @empty
                        <div class="text-center py-4 text-muted">
                            <p>Chưa có đánh giá nào.</p>
                        </div>
                    @endforelse
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

<style>
    /* Hiệu ứng cuộn mượt và chống bị header che khuất */
    html {
        scroll-behavior: smooth;
    }
    #review-section {
        scroll-margin-top: 100px;
    }

/* =====================
   THUMBNAIL ẢNH SẢN PHẨM
===================== */

.thumbnail-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}


.thumbnail-view {
    width: 330px;
    overflow: hidden;
}



.thumbnail-list {
    display: flex;
    gap: 10px;
    transition: transform 0.3s ease;
}



.thumbnail-item {
    width: 75px;
    height: 75px;
    flex: 0 0 75px;
    overflow: hidden;
    border-radius: 8px;
    border: 2px solid #eee;
}



.product-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
}



.product-thumbnail:hover {
    border-color: #ff6600;
}



.thumb-btn {

    width: 35px;
    height: 35px;

    border-radius: 50%;
    border: none;

    background: #333;
    color: white;

    font-size: 25px;

    display:flex;
    align-items:center;
    justify-content:center;

    cursor:pointer;

}



.thumb-btn:hover {

    background:#ff6600;

}
    /* Hiệu ứng nhấp nháy vàng nhanh gọn trong khoảng 0.8 giây */
    .highlight-review {
        animation: flashHighlight 0.8s ease-in-out;
    }
    @keyframes flashHighlight {
        0% { background-color: #fff3cd; transform: scale(1.01); }
        50% { background-color: #fff3cd; }
        100% { background-color: transparent; transform: scale(1); }
    }

    .chon-phien-ban input { display: none; }
    .hop-phien-ban { display: block; min-width: 150px; padding: 12px 15px; border: 2px solid #EEEEEE; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; background: #fff; }
    .chon-phien-ban input:checked + .hop-phien-ban { border: 2px solid var(--primary-color); background: #FFF6F0; }
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
    .star-rating input { display: none; }
    .star-rating label { color: #ddd; font-size: 1.5rem; padding: 0 0.1rem; cursor: pointer; transition: color 0.2s; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
</style>

<script>

// =====================
// BIẾN THỂ + GIÁ
// =====================
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('input[name="product_variant_id"]')
    .forEach(function(radio){
        radio.addEventListener('change', function(){

            updateVariant(this);

        })
    });
    let checkedVariant =
        document.querySelector('input[name="product_variant_id"]:checked');
    if(checkedVariant){
        updateVariant(checkedVariant);
    }
});
function updateVariant(radio){
    let price =
        Number(radio.dataset.price || 0);
    let salePrice =
        Number(radio.dataset.salePrice || 0);
    let discount =
        Number(radio.dataset.discount || 0);
    let priceText =
        document.getElementById('sale-price');
    let oldPrice =
        document.getElementById('old-price');
    let discountBall =
        document.getElementById('discount-ball');
    if(!priceText) return;
    let money =
        new Intl.NumberFormat('vi-VN');
    // GIÁ GIẢM
    if(
        salePrice > 0 &&
        salePrice < price
    ){
        priceText.innerHTML =
            money.format(salePrice)+' ₫';
        if(oldPrice){
            oldPrice.innerHTML 
                money.format(price)+' ₫';
            oldPrice.style.display='inline';
        }
    }else{
        priceText.innerHTML =
            money.format(price)+' ₫';
        if(oldPrice){
            oldPrice.innerHTML='';
            oldPrice.style.display='none';
        }
    }
    // BÓNG %
    if(discountBall){
        discountBall.style.display='none';
        if(
            salePrice > 0 &&
            salePrice < price &&
            discount > 0
        ){
            discountBall.style.display='flex';
            discountBall.innerHTML =
                '-'+discount+'%';
        }
    }
}
// =====================
// ĐỔI ẢNH LỚN
// =====================
function changeImage(src){
    let img =
        document.getElementById('main-product-image');
    if(img){
        img.src = src;
    }
}
// =====================
// SLIDE 4 ẢNH
// =====================
let thumbIndex = 0;
function nextThumbnail(){
    let list =
        document.getElementById('thumbnailList');
    if(!list) return;
    let total =
        list.querySelectorAll('.thumbnail-item').length;
    let max =
        Math.max(total - 4,0);
    if(thumbIndex < max){
        thumbIndex++;
    }
    moveThumbnail();
}
function prevThumbnail(){
    if(thumbIndex > 0){
        thumbIndex--;
    }
    moveThumbnail();
}
function moveThumbnail(){
    let list =
        document.getElementById('thumbnailList');
    if(!list) return;
    list.style.transform =
        `translateX(-${thumbIndex * 85}px)`;
}

</script>
@endpush