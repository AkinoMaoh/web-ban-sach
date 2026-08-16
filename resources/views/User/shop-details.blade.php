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


    {{-- =========================
ẢNH CHÍNH
========================= --}}

<div class="main-image mb-3 w-100">
    <img id="main-product-image"
         src="{{ asset('uploads/products/' . $product->image) }}"
         onclick="openZoom()"
         class="img-fluid rounded shadow"
         style="
            width:100%;
            height:520px;
            object-fit:contain;
            cursor:zoom-in;
         "
         alt="{{ $product->name }}">
</div>

{{-- =========================
DANH SÁCH ẢNH NHỎ
========================= --}}
@php
$productImages = $product->images->sortBy('sort_order')->values();
@endphp

@if($productImages->count() > 1)

<div class="thumbnail-wrapper position-relative">

<button type="button"
        class="thumb-btn prev"
        id="thumbPrev"
        onclick="prevThumbnail()">
    ‹
</button>

<div class="thumbnail-view">

    <div class="thumbnail-list" id="thumbnailList">

        @foreach($productImages as $image)

            <div class="thumbnail-item">

                <img src="{{ asset('uploads/products/' . $image->image) }}"
                     class="product-thumbnail"
                     data-src="{{ asset('uploads/products/' . $image->image) }}"
                     onmouseenter="previewImage(this)"
                     onclick="changeImage(this.dataset.src)"
                     alt="{{ $product->name }}">

            </div>

        @endforeach

    </div>

</div>

<button type="button"
        class="thumb-btn next"
        id="thumbNext"
        onclick="nextThumbnail()">
    ›
</button>


</div>

@endif
</div>
{{-- =========================
KHUNG ẢNH PHÓNG TO
========================= --}}

<div id="imageZoomOverlay" class="image-zoom-overlay">

<div class="zoom-box">


    {{-- Ảnh lớn --}}
    <div class="zoom-main">
        <img id="zoomMainImage"
             src=""
             alt="{{ $product->name }}">
    </div>

   

   

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

        @php
            $hasChecked = false;
        @endphp

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
                            @php
                                $hasChecked = true;
                            @endphp
                        @endif

                        required
                    >

                    <span class="hop-phien-ban">

                        {{-- Tên biến thể (Đã bỏ text-dark và thêm class variant-title) --}}
                        <strong class="d-block mb-1 variant-title">
                            {{ $bienThe->variant->name }}
                        </strong>

                        {{-- Giá --}}
                        <small class="d-block mb-1">

                            @if(
                                $bienThe->sale_price > 0 &&
                                $bienThe->sale_price < $bienThe->price
                            )

                                <span class="text-danger font-weight-bold">
                                    {{ number_format($bienThe->sale_price) }} VNĐ
                                </span>

                            @else

                                <span class="text-muted">
                                    {{ number_format($bienThe->price) }} VNĐ
                                </span>

                            @endif

                        </small>

                        {{-- Tồn kho --}}
                        <small class="{{ $bienThe->stock > 0 ? 'text-success' : 'text-danger' }} font-weight-bold">

                            {{ $bienThe->stock > 0
                                ? 'Còn ' . $bienThe->stock
                                : 'Hết hàng'
                            }}

                        </small>

                    </span>

                </label>

            @endif

        @endforeach

    </div>
</div>
           <!-- KHU VỰC VOUCHER HIỂN THỊ TRƯỚC KHI MUA -->
@if(isset($vouchers) && $vouchers->count() > 0)
    <div class="mt-4 pt-3 border-top mb-4">
        <h5 class="serif-font font-weight-bold mb-3">
            <i class="fas fa-tags text-orange mr-2"></i>Mã giảm giá dành cho bạn:
        </h5>
        <div class="d-flex overflow-auto pb-2 custom-scrollbar" style="gap: 12px;">
            @foreach($vouchers as $vc)
                <div class="voucher-ticket d-flex align-items-center flex-shrink-0" style="width: 260px; border: 1px solid #ff7a59; border-radius: 6px; background: #fff5f2;">
                    <div class="p-2 text-center" style="border-right: 1px dashed #ff7a59; background: #ff7a59; color: white; border-radius: 5px 0 0 5px; min-width: 70px;">
                        <h6 class="font-weight-bold mb-0" style="font-size: 13px;">{{ $vc->code }}</h6>
                    </div>
                    <div class="p-2 flex-grow-1">
                        <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 13px;">
                            @if($vc->type == 'percent')
                                Giảm {{ $vc->discount_value }}% 
                            @else
                                Giảm {{ number_format($vc->discount_value) }}đ
                            @endif
                        </h6>
                        <p class="text-muted mb-1" style="font-size: 11px;">Đơn tối thiểu: {{ number_format($vc->min_order_value) }}đ</p>
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 font-weight-bold" style="font-size: 11px; padding: 2px;" onclick="copyVoucherCode('{{ $vc->code }}')">
                            Copy Mã
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
<!-- END KHU VỰC VOUCHER -->

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
    <button type="button" class="btn btn-outline-danger btn-wishlist-v2" data-id="{{ $product->id }}">
        <i class="{{ in_array((int)$product->id, array_map('intval', $wishlistIds ?? [])) ? 'fas' : 'far' }} fa-heart"></i>
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
                        @if($i <= $roundedAvg) 
                            <i class="fas fa-star"></i> 
                        @else 
                            <i class="far fa-star"></i> 
                        @endif
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
                                @if($i <= $review->rating) 
                                    <i class="fas fa-star"></i> 
                                @else 
                                    <i class="far fa-star"></i> 
                                @endif
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
        
         {{-- BẮT ĐẦU PHẦN SẢN PHẨM LIÊN QUAN (SLIDER TRƯỢT) --}}
@if($relatedProducts->isNotEmpty())
    <div class="bg-white p-4 rounded shadow-sm border mb-4 related-products-section mt-5 pt-4 position-relative">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="serif-font font-weight-bold mb-0" style="color: var(--text-main); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; display: inline-block;">
                Sách cùng tác giả
            </h4>
            
            {{-- Nút mũi tên qua lại --}}
            <div class="slider-nav-btns">
                <button type="button" id="slidePrevBtn" class="btn btn-outline-secondary btn-sm rounded-circle">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" id="slideNextBtn" class="btn btn-outline-secondary btn-sm rounded-circle ml-2">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        {{-- Container trượt --}}
        <div class="related-slider-container" id="relatedSlider">
            @foreach($relatedProducts as $relProduct)
                <div class="related-slider-item">
                    <div class="card h-100 border-0 shadow-sm product-card-hover rounded-lg overflow-hidden position-relative">
                        
                        {{-- 1. NÚT WISHLIST NẰM NGOÀI THẺ <a> ĐỂ TRÁNH BỊ CHUYỂN TRANG --}}
                        <button type="button" 
                                class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist-v2 position-absolute" 
                                data-id="{{ $relProduct->id }}" 
                                style="top:10px; right:10px; width:34px; height:34px; border:none; z-index:20; display:flex; align-items:center; justify-content:center;">
                            <i class="{{ in_array((int)$relProduct->id, array_map('intval', $wishlistIds ?? [])) ? 'fas' : 'far' }} fa-heart" style="color:#D35400"></i>
                        </button>

                        {{-- 2. THẺ <a> CHỈ BỌC NỘI DUNG SẢN PHẨM --}}
                        <a href="{{ url('product/' . $relProduct->id) }}" class="text-decoration-none d-flex flex-column h-100">
                            <div class="position-relative text-center p-2 bg-white">
                                <img src="{{ asset('uploads/products/' . $relProduct->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $relProduct->name }}" 
                                     style="height: 180px; object-fit: contain;">
                            </div>
                            
                            <div class="card-body p-3 text-center bg-light">
                                <h6 class="card-title text-dark mb-2 text-truncate" title="{{ $relProduct->name }}" style="font-size: 14px; font-weight: 600;">
                                    {{ $relProduct->name }}
                                </h6>
                                
                                @php
                                    $relVariant = $relProduct->firstVariant;
                                    $relPrice = $relVariant ? $relVariant->price : $relProduct->price;
                                    $relSalePrice = $relVariant ? $relVariant->sale_price : 0;
                                @endphp

                                <div class="price-box">
                                    @if($relSalePrice > 0 && $relSalePrice < $relPrice)
                                        <span class="d-block font-weight-bold text-danger" style="font-size: 15px;">
                                            {{ number_format($relSalePrice, 0, ',', '.') }} ₫
                                        </span>
                                        <span class="text-muted" style="font-size: 12px; text-decoration: line-through;">
                                            {{ number_format($relPrice, 0, ',', '.') }} ₫
                                        </span>
                                    @else
                                        <span class="d-block font-weight-bold text-danger" style="font-size: 15px;">
                                            {{ number_format($relPrice, 0, ',', '.') }} ₫
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
{{-- KẾT THÚC PHẦN SẢN PHẨM LIÊN QUAN --}}
    </div>
</section>
@endsection

@push('scripts')
<!-- Thư viện cdnjs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
/* =========================================
1. CHUNG & CẤU TRÚC CUỘN
========================================= */
html {
    scroll-behavior: smooth;
}

#review-section {
    scroll-margin-top: 100px;
}

.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #ffbcab;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #ff7a59;
}

/* =========================================
2. SLIDER SÁCH LIÊN QUAN
========================================= */
.related-slider-container {
    display: flex !important;
    gap: 16px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding-bottom: 10px;
    -webkit-overflow-scrolling: touch;
}

.related-slider-item {
    display: flex;
    flex: 0 0 200px !important;
    max-width: 200px !important;
}

.related-slider-item .card {
    display: flex;
    flex-direction: column;
    width: 100%;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), 
                box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.related-slider-item .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
}

.related-slider-item .card-img-top {
    height: 180px;
    object-fit: contain;
    transition: transform 0.25s ease;
}

.related-slider-item .card:hover .card-img-top {
    transform: scale(1.02);
}

.related-slider-item .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
}

.related-slider-item .card-title {
    min-height: 40px; 
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

.related-slider-item .card:hover .card-title {
    color: #D35400 !important;
}

.related-slider-item .price-box {
    min-height: 42px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

/* =========================================
3. ẢNH CHÍNH & THUMBNAIL BÊN NGOÀI
========================================= */
.main-image {
    position: relative;
    width: 100%;
}

#main-product-image {
    display: block;
    width: 100%;
    height: 520px;
    object-fit: contain;
    cursor: zoom-in;
}

.thumbnail-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
}

.thumbnail-view {
    width: 340px;
    overflow: hidden;
}

.thumbnail-list {
    display: flex;
    gap: 10px;
    width: max-content;
    transition: transform .3s ease;
    will-change: transform;
}

.thumbnail-item {
    flex: 0 0 75px;
    width: 75px;
    height: 75px;
}

.product-thumbnail {
    display: block;
    width: 75px;
    height: 75px;
    object-fit: cover;
    border-radius: 6px;
    border: 2px solid #ddd;
    cursor: pointer;
    transition: .2s;
}

.product-thumbnail:hover {
    border-color: #007bff;
    transform: scale(1.05);
}

.thumb-btn {
    display: none;
    flex: 0 0 32px;
    width: 32px;
    height: 45px;
    padding: 0;
    border: none;
    border-radius: 6px;
    background: rgba(0, 0, 0, .65);
    color: #fff;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    z-index: 10;
}

.thumb-btn:hover {
    background: rgba(0, 0, 0, .9);
}

/* =========================================
4. POPUP ZOOM & THUMBNAIL POPUP
========================================= */
.image-zoom-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    pointer-events: none;
}

.zoom-main {
    position: relative;
    width: 600px;
    height: 600px;
    overflow: hidden;
    border-radius: 8px;
    background: #fff;
}

#zoomMainImage {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.05s ease-out;
    pointer-events: none;
}

.zoom-thumbnail-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
}

.zoom-thumbnail-view {
    width: 650px;
    overflow: hidden;
}

.zoom-thumbnail-list {
    display: flex;
    gap: 10px;
    width: max-content;
    transition: transform .3s ease;
    will-change: transform;
}

.zoom-thumbnail {
    flex: 0 0 80px;
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 7px;
    border: 2px solid #ddd;
    cursor: pointer;
    transition: .2s;
}

.zoom-thumbnail:hover,
.zoom-thumbnail.active {
    border-color: #007bff;
}

.zoom-thumbnail:hover {
    transform: scale(1.05);
}

.zoom-thumb-btn {
    display: none;
    flex: 0 0 35px;
    width: 35px;
    height: 45px;
    padding: 0;
    border: none;
    border-radius: 6px;
    background: rgba(0, 0, 0, .7);
    color: #fff;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
}

.zoom-thumb-btn:hover {
    background: rgba(0, 0, 0, .9);
}

/* =========================================
   BOX CHỌN PHIÊN BẢN (LIGHT & DARK MODE)
========================================= */

/* Tránh ẩn input hoàn toàn để trình duyệt vẫn focus được khi dùng phím Tab */
.chon-phien-ban input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* -----------------------------------------
   1. KHUNG THIẾT KẾ CƠ BẢN (LIGHT MODE)
----------------------------------------- */
.hop-phien-ban {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 145px;
    padding: 12px 16px;
    background-color: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    text-align: center;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Tên phiên bản: Ép màu đen chuẩn */
.hop-phien-ban .variant-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a !important;
    line-height: 1.3;
}

/* Giá tiền */
.hop-phien-ban small {
    font-size: 0.85rem;
}

.hop-phien-ban .text-danger {
    color: #ea580c !important; /* Cam nổi bật */
}

.hop-phien-ban .text-muted {
    color: #64748b !important;
}

/* Tồn kho */
.hop-phien-ban .text-success {
    color: #16a34a !important;
}

/* HOVER (Rê chuột) - Light Mode */
.chon-phien-ban:hover .hop-phien-ban {
    border-color: #fb923c;
    background-color: #fff7ed;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.1);
}

/* CHECKED (Được chọn) - Light Mode */
.chon-phien-ban input:checked + .hop-phien-ban {
    border-color: #ea580c;
    background-color: #fff7ed;
    box-shadow: 0 0 0 1px #ea580c, 0 4px 12px rgba(234, 88, 12, 0.15);
}

/* DISABLED (Hết hàng / Khóa) */
.chon-phien-ban input:disabled + .hop-phien-ban {
    opacity: 0.55;
    background-color: #f1f5f9;
    border-color: #cbd5e1;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}


/* -----------------------------------------
   2. TỐI ƯU GIAO DIỆN DARK MODE
----------------------------------------- */
html.dark .hop-phien-ban,
body.dark .hop-phien-ban,
.dark .hop-phien-ban,
[data-theme="dark"] .hop-phien-ban {
    background-color: #27272a !important;
    border-color: #3f3f46 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
}

/* Ép tên phiên bản luôn đen cả ở Dark Mode theo yêu cầu */
html.dark .hop-phien-ban .variant-title,
body.dark .hop-phien-ban .variant-title,
.dark .hop-phien-ban .variant-title,
[data-theme="dark"] .hop-phien-ban .variant-title {
    color: #000000 !important;
}

/* Giá & Tồn kho trên Dark Mode */
html.dark .hop-phien-ban .text-muted,
body.dark .hop-phien-ban .text-muted,
.dark .hop-phien-ban .text-muted {
    color: #a1a1aa !important;
}

html.dark .hop-phien-ban .text-danger,
body.dark .hop-phien-ban .text-danger,
.dark .hop-phien-ban .text-danger {
    color: #fb923c !important;
}

html.dark .hop-phien-ban .text-success,
body.dark .hop-phien-ban .text-success,
.dark .hop-phien-ban .text-success {
    color: #4ade80 !important;
}

/* HOVER - Dark Mode */
html.dark .chon-phien-ban:hover .hop-phien-ban,
body.dark .chon-phien-ban:hover .hop-phien-ban,
.dark .chon-phien-ban:hover .hop-phien-ban {
    background-color: #3f3f46 !important;
    border-color: #f97316 !important;
    transform: translateY(-2px);
}

/* CHECKED - Dark Mode */
html.dark .chon-phien-ban input:checked + .hop-phien-ban,
body.dark .chon-phien-ban input:checked + .hop-phien-ban,
.dark .chon-phien-ban input:checked + .hop-phien-ban {
    background: linear-gradient(135deg, rgba(234, 88, 12, 0.25), rgba(249, 115, 22, 0.15)) !important;
    border-color: #f97316 !important;
    box-shadow: 0 0 0 1px #f97316, 0 4px 16px rgba(249, 115, 22, 0.25) !important;
}

/* Đổi màu giá & tồn kho thành trắng khi được chọn trên Dark Mode */
html.dark .chon-phien-ban input:checked + .hop-phien-ban .text-muted,
html.dark .chon-phien-ban input:checked + .hop-phien-ban .text-danger,
html.dark .chon-phien-ban input:checked + .hop-phien-ban .text-success,
body.dark .chon-phien-ban input:checked + .hop-phien-ban .text-muted,
body.dark .chon-phien-ban input:checked + .hop-phien-ban .text-danger,
body.dark .chon-phien-ban input:checked + .hop-phien-ban .text-success,
.dark .chon-phien-ban input:checked + .hop-phien-ban .text-muted,
.dark .chon-phien-ban input:checked + .hop-phien-ban .text-danger,
.dark .chon-phien-ban input:checked + .hop-phien-ban .text-success {
    color: #ffffff !important;
}

/* DISABLED - Dark Mode */
html.dark .chon-phien-ban input:disabled + .hop-phien-ban,
body.dark .chon-phien-ban input:disabled + .hop-phien-ban,
.dark .chon-phien-ban input:disabled + .hop-phien-ban {
    opacity: 0.4;
    background-color: #18181b !important;
    border-color: #27272a !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Helper hiển thị Toastr Notification
    function showNotification(type, message) {
        if (typeof toastr !== 'undefined') {
            toastr.options = { "positionClass": "toast-bottom-right", "timeOut": "2000" };
            if (type === 'success') toastr.success(message);
            else if (type === 'info') toastr.info(message);
            else if (type === 'warning') toastr.warning(message);
            else toastr.error(message);
        } else {
            alert(message);
        }
    }

    // 1. XỬ LÝ YÊU THÍCH (WISHLIST AJAX - ĐÃ TỐI ƯU VÀ CHỐNG TRÙNG EVENT)
    $(document).off('click', '.btn-wishlist-v2').on('click', '.btn-wishlist-v2', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const button = $(this);
        const productId = button.data('id') || button.attr('data-id');

        if (!productId) {
            console.error('Không tìm thấy Product ID!');
            return;
        }

        $.ajax({
            url: "{{ route('user.wishlist.toggle') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId
            },
            success: function (response) {
                const targetButtons = $('.btn-wishlist-v2[data-id="' + productId + '"]');
                const targetIcons = targetButtons.find('i');

                if (response.status === 'added') {
                    targetIcons.attr('class', 'fas fa-heart');
                    showNotification('success', response.message || 'Đã thêm vào danh sách yêu thích!');
                } else if (response.status === 'removed') {
                    targetIcons.attr('class', 'far fa-heart');
                    showNotification('info', response.message || 'Đã gỡ khỏi danh sách yêu thích!');
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    showNotification('warning', 'Vui lòng đăng nhập để thực hiện!');
                } else {
                    showNotification('error', 'Có lỗi xảy ra, vui lòng thử lại!');
                }
            }
        });
    });

    // 2. SLIDER SÁCH LIÊN QUAN
    const sliderContainer = document.getElementById("relatedSlider");
    const prevBtn = document.getElementById("slidePrevBtn");
    const nextBtn = document.getElementById("slideNextBtn");

    if (sliderContainer && prevBtn && nextBtn) {
        const scrollAmount = 300;
        prevBtn.addEventListener("click", () => sliderContainer.scrollBy({ left: -scrollAmount, behavior: "smooth" }));
        nextBtn.addEventListener("click", () => sliderContainer.scrollBy({ left: scrollAmount, behavior: "smooth" }));
    }

    // 3. COPY MÃ VOUCHER
    window.copyVoucherCode = function(code) {
        navigator.clipboard.writeText(code).then(() => {
            showNotification('success', `Đã copy mã: ${code}<br>Hãy dán mã này ở trang Thanh toán nhé!`);
        }).catch(err => {
            console.error('Lỗi khi copy: ', err);
            showNotification('error', 'Lỗi trình duyệt, không thể copy!');
        });
    };

    // 4. XỬ LÝ BIẾN THỂ VÀ GIÁ SẢN PHẨM
    const moneyFormatter = new Intl.NumberFormat('vi-VN');
    
    function updateVariant(radio) {
        const price = Number(radio.dataset.price || 0);
        const salePrice = Number(radio.dataset.salePrice || 0);
        const discount = Number(radio.dataset.discount || 0);

        const priceText = document.getElementById('sale-price');
        const oldPrice = document.getElementById('old-price');
        const discountBall = document.getElementById('discount-ball');

        if (!priceText) return;

        const hasSale = salePrice > 0 && salePrice < price;
        priceText.innerHTML = moneyFormatter.format(hasSale ? salePrice : price) + ' ₫';

        if (oldPrice) {
            oldPrice.style.display = hasSale ? 'inline' : 'none';
            oldPrice.innerHTML = hasSale ? moneyFormatter.format(price) + ' ₫' : '';
        }

        if (discountBall) {
            discountBall.style.display = (hasSale && discount > 0) ? 'flex' : 'none';
            discountBall.innerHTML = `-${discount}%`;
        }
    }

    document.querySelectorAll('input[name="product_variant_id"]').forEach(radio => {
        radio.addEventListener('change', function () { updateVariant(this); });
    });
    const checkedVariant = document.querySelector('input[name="product_variant_id"]:checked');
    if (checkedVariant) updateVariant(checkedVariant);

    // 5. XỬ LÝ GALLERY & PHÓNG TO (ZOOM) ẢNH
    const mainImage = document.getElementById('main-product-image');
    const overlay = document.getElementById('imageZoomOverlay');
    const zoomImage = document.getElementById('zoomMainImage');
    const ZOOM_LEVEL = 2.5;

    window.changeImage = (src) => { if (mainImage) mainImage.src = src; };
    window.previewImage = (img) => changeImage(img.dataset.src || img.src);

    window.openZoom = function (src) {
        if (!overlay || !zoomImage) return;
        zoomImage.src = src || mainImage.src;
        overlay.style.display = 'flex';
        zoomThumbIndex = 0;
        updateZoomActiveThumbnail(zoomImage.src);
        resetZoomThumbnail();
    };

    window.closeZoom = function () {
        if (zoomImage) {
            zoomImage.style.transform = 'scale(1)';
            zoomImage.style.transformOrigin = 'center center';
        }
        if (overlay) overlay.style.display = 'none';
    };

    if (mainImage) {
        mainImage.addEventListener('mouseenter', function () { openZoom(this.src); });
        mainImage.addEventListener('mousemove', function (e) {
            if (!overlay || !zoomImage) return;
            if (overlay.style.display !== 'flex') openZoom(this.src);

            const rect = mainImage.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;

            zoomImage.style.transformOrigin = `${x}% ${y}%`;
            zoomImage.style.transform = `scale(${ZOOM_LEVEL})`;
        });
        mainImage.addEventListener('mouseleave', closeZoom);
    }

    const zoomBox = document.querySelector('.zoom-box');
    if (zoomBox) zoomBox.addEventListener('mouseleave', closeZoom);
    if (overlay) {
        overlay.addEventListener('click', (e) => { if (e.target === overlay) closeZoom(); });
    }

    // 6. THUMBNAILS SLIDER (NGOÀI VÀ POPUP)
    let thumbIndex = 0, zoomThumbIndex = 0;
    const thumbnailList = document.getElementById('thumbnailList');
    const thumbnailView = document.querySelector('.thumbnail-view');
    const zoomThumbnailList = document.getElementById('zoomThumbnailList');
    const zoomThumbnailView = document.querySelector('.zoom-thumbnail-view');

    window.previewZoomImage = window.changeZoomImage = function (imgOrSrc) {
        if (!zoomImage) return;
        const src = typeof imgOrSrc === 'string' ? imgOrSrc : (imgOrSrc.dataset.src || imgOrSrc.src);
        zoomImage.src = src;
        updateZoomActiveThumbnail(src);
    };

    function updateZoomActiveThumbnail(src) {
        document.querySelectorAll('.zoom-thumbnail').forEach(img => {
            img.classList.toggle('active', (img.dataset.src || img.src) === src);
        });
    }

    function moveSlider(list, view, index, step) {
        if (!list || !view) return;
        const maxMove = Math.max(list.scrollWidth - view.clientWidth, 0);
        let move = Math.min(index * step, maxMove);
        list.style.transform = `translateX(-${move}px)`;
    }

    window.nextThumbnail = () => {
        if (thumbnailList && thumbnailView && (thumbIndex * 85) < (thumbnailList.scrollWidth - thumbnailView.clientWidth)) thumbIndex++;
        moveSlider(thumbnailList, thumbnailView, thumbIndex, 85);
    };
    window.prevThumbnail = () => {
        if (thumbIndex > 0) thumbIndex--;
        moveSlider(thumbnailList, thumbnailView, thumbIndex, 85);
    };

    window.zoomNextImage = () => {
        if (zoomThumbnailList && zoomThumbnailView && (zoomThumbIndex * 90) < (zoomThumbnailList.scrollWidth - zoomThumbnailView.clientWidth)) zoomThumbIndex++;
        moveSlider(zoomThumbnailList, zoomThumbnailView, zoomThumbIndex, 90);
    };
    window.zoomPrevImage = () => {
        if (zoomThumbIndex > 0) zoomThumbIndex--;
        moveSlider(zoomThumbnailList, zoomThumbnailView, zoomThumbIndex, 90);
    };

    function resetZoomThumbnail() {
        if (zoomThumbnailList) zoomThumbnailList.style.transform = 'translateX(0)';
    }

    function updateThumbnailButtons() {
        const check = (list, view, prevId, nextId) => {
            const prev = document.getElementById(prevId), next = document.getElementById(nextId);
            if (list && view && prev && next) {
                const canSlide = list.scrollWidth > view.clientWidth + 1;
                prev.style.display = next.style.display = canSlide ? 'block' : 'none';
            }
        };
        check(thumbnailList, thumbnailView, 'thumbPrev', 'thumbNext');
        check(zoomThumbnailList, zoomThumbnailView, 'zoomPrev', 'zoomNext');
    }

    updateThumbnailButtons();
    window.addEventListener('resize', () => {
        updateThumbnailButtons();
        moveSlider(thumbnailList, thumbnailView, thumbIndex, 85);
        moveSlider(zoomThumbnailList, zoomThumbnailView, zoomThumbIndex, 90);
    });

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeZoom(); });
});
</script>
@endpush