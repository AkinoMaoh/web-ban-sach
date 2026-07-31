@extends('layout.user')

@section('content')
@php
    // Kiểm tra đăng nhập và lấy danh sách ID sản phẩm đã yêu thích
    $wishlistIds = [];
    if(Auth::check()) {
        $wishlistIds = \Illuminate\Support\Facades\DB::table('wishlists')
            ->where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();
    }
@endphp

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
                    <div class="col-lg-7 pl-lg-5 mt-4 mt-lg-0">
                        <h1 class="product-title serif-font font-weight-bold mb-2" style="line-height: 1.4; font-size: 26px;">
                            {{ $product->name }}
                        </h1>
                        
                        @php
                            $variant = $product->variants->where('stock', '>', 0)->first() ?? $product->variants->first();
                            $hasSale = $variant && $variant->sale_price > 0 && $variant->sale_price < $variant->price;
                            $currentPrice = $hasSale ? $variant->sale_price : ($variant?->price ?? $product->price);
                            $oldPrice = $variant?->price ?? 0;
                            $discountPercent = $hasSale ? round((($oldPrice - $currentPrice) / $oldPrice) * 100) : 0;
                        @endphp

                        <!-- Giá sản phẩm -->
                        <div class="price-box-container mb-4 d-flex align-items-center flex-wrap pt-2 pb-2">
                            <span id="sale-price" class="font-weight-bold" style="color: #ee4d2d; font-size: 34px; line-height: 1;">
                                {{ number_format($currentPrice, 0, ',', '.') }} ₫
                            </span>
                            <span id="old-price" class="ml-3 text-muted" style="font-size: 18px; text-decoration: line-through; {{ $hasSale ? '' : 'display:none;' }}">
                                {{ number_format($oldPrice, 0, ',', '.') }} ₫
                            </span>
                            <span id="discount-badge" class="badge ml-3" style="font-size: 12px; font-weight: 600; padding: 4px 6px; background-color: #fcebeb; color: #ee4d2d; border: 1px solid #ee4d2d; border-radius: 2px; {{ $hasSale ? '' : 'display:none;' }}">
                                GIẢM {{ $discountPercent }}%
                            </span>
                        </div>

                        <!-- Thông tin thuộc tính -->
                        <div class="product-meta mb-4 text-muted" style="font-size: 14px;">
                            <div class="row mb-2">
                                <div class="col-4 col-md-3">Tác giả:</div>
                                <div class="col-8 col-md-9 text-dark font-weight-bold">{{ $product->author->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 col-md-3">NXB:</div>
                                <div class="col-8 col-md-9 text-dark">{{ $product->publishers->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 col-md-3">Danh mục:</div>
                                <div class="col-8 col-md-9 text-dark">{{ $product->category->name }}</div>
                            </div>
                        </div>
                        
                        <!-- Mô tả ngắn -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-dark mb-2" style="font-size: 15px;">Mô tả sản phẩm:</h6>
                            <p class="text-muted" style="line-height: 1.6; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Box Chọn Phiên Bản -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="font-weight-bold mb-3 text-dark" style="font-size: 15px;">Chọn phiên bản:</h6>
                            <div class="d-flex flex-wrap mb-4" style="gap: 12px;">
                                @php $hasChecked = false; @endphp
                                @foreach($product->variants as $bienThe)
                                    @if($bienThe->price > 0)
                                        <label class="chon-phien-ban mb-0">
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
                                            
                                            <!-- Khối box phiên bản đã được fix cứng chiều cao -->
                                            <span class="hop-phien-ban position-relative">
                                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center">
                                                    <strong class="d-block mb-1 text-dark" style="font-size: 14px;">{{ $bienThe->edition }}</strong>
                                                    
                                                    <div class="variant-price-info w-100">
                                                        @if($bienThe->sale_price > 0 && $bienThe->sale_price < $bienThe->price)
                                                            <span class="text-danger font-weight-bold d-block" style="font-size: 15px;">{{ number_format($bienThe->sale_price, 0, ',', '.') }} ₫</span>
                                                            <del class="text-muted d-block" style="font-size: 12px;">{{ number_format($bienThe->price, 0, ',', '.') }} ₫</del>
                                                        @else
                                                            <span class="text-dark font-weight-bold d-block" style="font-size: 15px;">{{ number_format($bienThe->price, 0, ',', '.') }} ₫</span>
                                                            <del class="d-block" style="font-size: 12px; visibility: hidden;">0 ₫</del>
                                                        @endif
                                                    </div>

                                                    <div class="mt-1 {{ $bienThe->stock > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 11px; font-weight: 500;">
                                                        {{ $bienThe->stock > 0 ? 'Còn ' . $bienThe->stock . ' sp' : 'Hết hàng' }}
                                                    </div>
                                                </div>

                                                <!-- Dấu tick góc phải chuẩn Shopee -->
                                                <div class="check-mark">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Chọn Số Lượng & Yêu Thích -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold mb-3 text-dark" style="font-size: 15px;">Số lượng:</h6>
                            <div class="d-flex align-items-center">
                                <div class="input-group mr-3" style="width: 130px;">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-secondary font-weight-bold" style="border-color: #ddd;" type="button" onclick="let q=document.getElementById('o-so-luong'); if(q.value>1)q.value--; q.dispatchEvent(new Event('input'))">-</button>
                                    </div>
                                    <input type="number" name="quantity" id="o-so-luong" class="form-control text-center font-weight-bold bg-white" style="border-color: #ddd;" value="1" min="1" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary font-weight-bold" style="border-color: #ddd;" type="button" onclick="let q=document.getElementById('o-so-luong'); q.value++; q.dispatchEvent(new Event('input'))">+</button>
                                    </div>
                                </div>
                                
                                <!-- Nút Wishlist (Đã chuyển class thành btn-wishlist-v2) -->
                                <button type="button" class="btn btn-light shadow-sm btn-wishlist-v2 border d-flex align-items-center justify-content-center" data-id="{{ $product->id }}" style="width: 42px; height: 42px; border-radius: 6px;" title="Thêm vào yêu thích">
                                    <i class="{{ in_array($product->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart text-danger" style="font-size: 18px;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Nút Hành Động -->
                        <div class="d-flex align-items-center mt-4 pt-4 border-top">
                            <button type="submit" name="action_type" value="add_to_cart" class="btn btn-outline-danger px-4 py-3 font-weight-bold mr-3 shadow-sm d-flex align-items-center" style="border-width: 1px; background-color: #fcebeb;">
                                <i class="fas fa-cart-plus mr-2" style="font-size: 18px;"></i> Thêm vào giỏ
                            </button>
                            <button type="submit" name="action_type" value="buy_now" class="btn px-5 py-3 font-weight-bold shadow-sm mr-3 text-white" style="background-color: #ee4d2d; border: none;">
                                Mua ngay
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

                <div class="comments-list mt-4 pt-3 border-top">
                    @forelse($product->reviews as $review)
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

        {{-- BẮT ĐẦU PHẦN SẢN PHẨM LIÊN QUAN (SLIDER TRƯỢT) --}}
        @if($relatedProducts->isNotEmpty())
            <div class="related-products-section mt-5 pt-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="serif-font font-weight-bold mb-0" style="color: var(--text-main); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; display: inline-block;">
                        Sách cùng tác giả
                    </h4>
                    
                    {{-- Nút mũi tên qua lại --}}
                    <div class="slider-nav-btns d-none d-md-block">
                        <button type="button" class="btn btn-outline-secondary rounded-circle mr-1" onclick="slideRelated(-1)" style="width: 35px; height: 35px; padding: 0;">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary rounded-circle" onclick="slideRelated(1)" style="width: 35px; height: 35px; padding: 0;">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Container trượt --}}
                <div class="related-slider-container" id="relatedSlider">
                    @foreach($relatedProducts as $relProduct)
                        <div class="related-slider-item">
                            <div class="card h-100 border-0 shadow-sm product-card-hover rounded-lg overflow-hidden">
                                <a href="{{ url('shop-details/' . $relProduct->id) }}" class="text-decoration-none">
                                    <div class="position-relative text-center p-2 bg-white">
                                        <!-- Wishlist Mới (Sách liên quan) -->
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-wishlist-v2 position-absolute" data-id="{{ $relProduct->id }}" style="top:10px;right:10px;width:34px;height:34px;border:none;z-index:10;display:flex;align-items:center;justify-content:center;">
                                            <i class="{{ in_array($relProduct->id, $wishlistIds ?? []) ? 'fas' : 'far' }} fa-heart" style="color:#D35400"></i>
                                        </button>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    /* Hiệu ứng cuộn mượt chung */
    html { scroll-behavior: smooth; }
    #review-section { scroll-margin-top: 100px; }

    /* =====================
       THUMBNAIL ẢNH SẢN PHẨM
    ===================== */
    .thumbnail-wrapper { display: flex; align-items: center; justify-content: center; gap: 10px; }
    .thumbnail-view { width: 330px; overflow: hidden; }
    .thumbnail-list { display: flex; gap: 10px; transition: transform 0.3s ease; }
    .thumbnail-item { width: 75px; height: 75px; flex: 0 0 75px; overflow: hidden; border-radius: 8px; border: 2px solid #eee; }
    .product-thumbnail { width: 100%; height: 100%; object-fit: cover; cursor: pointer; }
    .product-thumbnail:hover { border-color: #ff6600; }
    .thumb-btn { width: 35px; height: 35px; border-radius: 50%; border: none; background: #333; color: white; font-size: 25px; display:flex; align-items:center; justify-content:center; cursor:pointer; }
    .thumb-btn:hover { background:#ff6600; }

    .highlight-review { animation: flashHighlight 0.8s ease-in-out; }
    @keyframes flashHighlight {
        0% { background-color: #fff3cd; transform: scale(1.01); }
        50% { background-color: #fff3cd; }
        100% { background-color: transparent; transform: scale(1); }
    }

    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
    .star-rating input { display: none; }
    .star-rating label { color: #ddd; font-size: 1.5rem; padding: 0 0.1rem; cursor: pointer; transition: color 0.2s; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }

    /* =====================
       GIAO DIỆN CHỌN PHIÊN BẢN 
    ===================== */
    .chon-phien-ban { cursor: pointer; display: inline-block; }
    .chon-phien-ban input { display: none; }
    .hop-phien-ban { 
        display: block; width: 130px; height: 105px; padding: 10px 5px; 
        border: 1px solid #e0e0e0; border-radius: 4px; transition: all 0.2s ease; 
        background: #fff; overflow: hidden;
    }
    .hop-phien-ban:hover { border-color: #ee4d2d; }
    .chon-phien-ban input:checked + .hop-phien-ban { border: 1px solid #ee4d2d; background: #fff; }
    
    .check-mark {
        position: absolute; bottom: 0; right: 0; width: 0; height: 0;
        border-bottom: 24px solid #ee4d2d; border-left: 24px solid transparent; display: none;
    }
    .check-mark i {
        position: absolute; bottom: -24px; right: 1px; color: white; font-size: 9px;
    }
    .chon-phien-ban input:checked + .hop-phien-ban .check-mark { display: block; }
    .chon-phien-ban input:disabled + .hop-phien-ban { opacity: 0.5; cursor: not-allowed; background: #fafafa; }
    .chon-phien-ban input:disabled + .hop-phien-ban:hover { border-color: #e0e0e0; }

    /* =====================
       FIX MÀU TEXT TÊN SÁCH (DARK MODE)
    ===================== */
    .product-title { color: #222; }
    body.dark-mode .product-title, body.dark-theme .product-title, [data-theme="dark"] .product-title, .dark-mode .product-title { color: #ffffff !important; }
    @media (prefers-color-scheme: dark) { .product-title { color: #ffffff !important; } }

    /* =====================
       SẢN PHẨM LIÊN QUAN (SLIDER) 
    ===================== */
    .related-slider-container {
        display: flex; overflow-x: auto; gap: 20px; padding-bottom: 20px; 
        -ms-overflow-style: none; scrollbar-width: none; scroll-snap-type: x mandatory;
    }
    .related-slider-container::-webkit-scrollbar { display: none; }
    .related-slider-item { flex: 0 0 calc(20% - 16px); scroll-snap-align: start; }
    
    @media (max-width: 991px) { .related-slider-item { flex: 0 0 calc(33.333% - 14px); } }
    @media (max-width: 767px) { .related-slider-item { flex: 0 0 calc(50% - 10px); } }
    
    .product-card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .product-card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .product-card-hover .card-title { transition: color 0.2s; }
    .product-card-hover:hover .card-title { color: var(--primary-color) !important; }
</style>

<script>
// =====================
// SẢN PHẨM LIÊN QUAN (SLIDER) - VÒNG LẶP VÔ TẬN & LƯỚT MƯỢT
// =====================
const slider = document.getElementById('relatedSlider');
let autoSlideInterval;
const autoSlideDelay = 3500; // Thời gian chờ giữa các lần trượt (3.5s)
let isSliding = false; // Biến chặn spam click

// Hàm lướt mượt vật lý và nhận Callback
function smoothScroll(element, target, duration, callback) {
    element.style.scrollSnapType = 'none'; // Tắt khóa khung tạm thời để lướt mượt
    
    let start = element.scrollLeft;
    let change = target - start;
    let startTime = performance.now();

    function animateScroll(currentTime) {
        let elapsed = currentTime - startTime;
        let progress = elapsed / duration;
        if (progress > 1) progress = 1;
        
        // Công thức Easing (Nhanh dần rồi hãm phanh mượt ở cuối)
        let ease = progress < 0.5 ? 2 * progress * progress : -1 + (4 - 2 * progress) * progress;
        element.scrollLeft = start + change * ease;

        if (elapsed < duration) {
            requestAnimationFrame(animateScroll);
        } else {
            element.style.scrollSnapType = 'x mandatory'; // Bật lại khóa khung khi dừng
            if (callback) callback();
        }
    }
    requestAnimationFrame(animateScroll);
}

function slideRelated(direction) {
    if(!slider || isSliding) return;
    
    // Nếu số lượng SP ít không đủ tràn màn hình thì không cần Loop
    if (slider.scrollWidth <= slider.clientWidth) return;

    const item = slider.querySelector('.related-slider-item');
    if(!item) return;
    
    isSliding = true;
    const scrollAmount = item.offsetWidth + 30; // 20 là gap

    if (direction === 1) {
        // TRƯỢT SANG PHẢI (TỚI TỚI)
        smoothScroll(slider, slider.scrollLeft + scrollAmount, 500, () => {
            // Lướt xong -> Ném ngay phần tử đầu tiên xuống cuối hàng
            slider.appendChild(slider.firstElementChild);
            // Kéo tịnh tiến khoảng cách lại để màn hình giữ nguyên vị trí, không gây giật
            slider.scrollLeft -= scrollAmount;
            isSliding = false;
        });
    } else {
        // TRƯỢT SANG TRÁI (BẤM NÚT BACK LÙI LẠI)
        // Mượn thằng cuối cùng lên nhét vào đầu tiên trước khi lướt
        slider.insertBefore(slider.lastElementChild, slider.firstElementChild);
        slider.scrollLeft += scrollAmount;
        // Sau đó trượt mượt mà lùi lại
        smoothScroll(slider, slider.scrollLeft - scrollAmount, 500, () => {
            isSliding = false;
        });
    }
}

// Bắt đầu chạy Auto-slide
function startAutoSlide() {
    if(!slider) return;
    autoSlideInterval = setInterval(function() {
        slideRelated(1); 
    }, autoSlideDelay);
}

function stopAutoSlide() {
    clearInterval(autoSlideInterval);
}

// Khởi chạy khi load xong DOM
if (slider) {
    startAutoSlide();
    slider.addEventListener('mouseenter', stopAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);
    slider.addEventListener('touchstart', stopAutoSlide, { passive: true });
    slider.addEventListener('touchend', startAutoSlide);
}


// =====================
// BIẾN THỂ + GIÁ
// =====================
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('input[name="product_variant_id"]')
    .forEach(function(radio){
        radio.addEventListener('change', function(){
            updateVariant(this);
        });
    });

    let checkedVariant = document.querySelector('input[name="product_variant_id"]:checked');
    if(checkedVariant) { updateVariant(checkedVariant); }
});

function updateVariant(radio){
    let price = Number(radio.dataset.price || 0);
    let salePrice = Number(radio.dataset.salePrice || 0);
    let discount = Number(radio.dataset.discount || 0);
    
    let priceText = document.getElementById('sale-price');
    let oldPrice = document.getElementById('old-price');
    let discountBall = document.getElementById('discount-ball');
    let discountBadge = document.getElementById('discount-badge'); 

    if(!priceText) return;
    let money = new Intl.NumberFormat('vi-VN');

    if(salePrice > 0 && salePrice < price){
        priceText.innerHTML = money.format(salePrice)+' ₫';
        if(oldPrice){
            oldPrice.innerHTML = money.format(price)+' ₫';
            oldPrice.style.display='inline-block';
        }
        let percent = Math.round(((price - salePrice) / price) * 100);
        if(discountBadge){
            discountBadge.style.display='inline-block';
            discountBadge.innerHTML = 'GIẢM ' + (discount > 0 ? discount : percent) + '%';
        }
        if(discountBall){
            discountBall.style.display='flex';
            discountBall.innerHTML = '-' + (discount > 0 ? discount : percent) + '%';
        }
    } else {
        priceText.innerHTML = money.format(price)+' ₫';
        if(oldPrice) oldPrice.style.display='none';
        if(discountBadge) discountBadge.style.display='none';
        if(discountBall) discountBall.style.display='none';
    }
}

// =====================
// ĐỔI ẢNH LỚN
// =====================
function changeImage(src){
    let img = document.getElementById('main-product-image');
    if(img) img.src = src;
}

// =====================
// SLIDE 4 ẢNH THUMBNAIL
// =====================
let thumbIndex = 0;
function nextThumbnail(){
    let list = document.getElementById('thumbnailList');
    if(!list) return;
    let total = list.querySelectorAll('.thumbnail-item').length;
    let max = Math.max(total - 4,0);
    if(thumbIndex < max) thumbIndex++;
    moveThumbnail();
}
function prevThumbnail(){
    if(thumbIndex > 0) thumbIndex--;
    moveThumbnail();
}
function moveThumbnail(){
    let list = document.getElementById('thumbnailList');
    if(!list) return;
    list.style.transform = `translateX(-${thumbIndex * 85}px)`;
}

// =====================
// LOGIC WISHLIST V2 
// =====================
document.addEventListener('DOMContentLoaded', function () {
    // Cấu hình Toastr
    toastr.options = { 
        "closeButton": true, 
        "progressBar": true, 
        "positionClass": "toast-bottom-right", 
        "timeOut": "2500" 
    };

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

            // Tìm toàn bộ các nút của CÙNG 1 SẢN PHẨM trên giao diện (Cho trường hợp click ở Sản phẩm liên quan)
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
@endpush