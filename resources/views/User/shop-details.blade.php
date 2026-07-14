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
                    <div class="col-lg-5 mb-4 mb-lg-0 text-center">
                        <img src="{{ asset('uploads/products/' . $product->image) }}" class="img-fluid rounded shadow" alt="{{ $product->name }}" style="max-height: 500px; object-fit: contain;">
                    </div>
                    
                    <!-- Thông tin Sách -->
                    <div class="col-lg-7 pl-lg-5">
                        <h1 class="serif-font font-weight-bold mb-3" style="color: var(--text-main); line-height: 1.3;">{{ $product->name }}</h1>
                        
                        <h2 class="display-4 font-weight-bold mb-4" style="color: var(--primary-color);">
                            <span id="hien-thi-gia">{{ number_format($product->price) }} VNĐ</span>
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
                                                data-gia="{{ $bienThe->price }}" 
                                                data-ton-kho="{{ $bienThe->stock }}" 
                                                {{ $bienThe->stock <= 0 ? 'disabled' : '' }}
                                                @if(!$hasChecked && $bienThe->stock > 0)
                                                    checked
                                                    @php $hasChecked = true; @endphp
                                                @endif
                                                required>
                                            <span class="hop-phien-ban">
                                                <strong class="d-block mb-1 text-dark">{{ $bienThe->edition }}</strong>
                                                <small class="d-block text-muted mb-1">{{ number_format($bienThe->price) }} VNĐ</small>
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
        <div class="card p-3 p-md-4 border-0 shadow-sm rounded-3 mt-4">
            <h5 class="serif-font font-weight-bold mb-1">Đánh giá & Nhận xét</h5>
            <div class="border-top pt-4 mt-3">  
                <div class="row align-items-center mb-4">
                    
                    <!-- Cột 1: Điểm số trung bình -->
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

                    <!-- Cột 2: Đồ thị phần trăm -->
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

                    <!-- Cột 3: Trạng thái / Form đánh giá -->
                    <div class="col-md-5 col-lg-7 ps-md-4 text-start py-2">
                        @guest
                            <div class="bg-light p-3 rounded text-center">
                                <p class="text-muted mb-2">Chỉ có thành viên mới có thể viết nhận xét.</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Đăng nhập</a>
                                <span class="mx-2 text-muted">hoặc</span>
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold">Đăng ký</a>
                            </div>
                        @else
                            @php 
                                $hasBought = Auth::user()->hasBoughtProduct($product->id);
                                $unreviewedDetails = Auth::user()->getUnreviewedOrderDetails($product->id);
                            @endphp

                            @if(!$hasBought)
                                <div class="bg-light p-4 rounded text-center shadow-sm border border-white">
                                    <i class="fas fa-shopping-cart fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                                    <p class="text-muted mb-0 font-weight-bold">Bạn chưa mua sản phẩm này</p>
                                    <small class="text-muted">Tính năng đánh giá chỉ dành cho khách hàng đã mua.</small>
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
                                        <input type="radio" id="star5" name="rating" value="5"><label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                                    </div>
                                    <textarea class="form-control mb-2 shadow-sm" name="comment" rows="2" placeholder="Nhận xét của bạn..." required style="resize: none;"></textarea>
                                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4">Gửi nhận xét</button>
                                </form>
                            @else
                                <div class="bg-light p-4 rounded text-center shadow-sm border border-white">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success" style="opacity: 0.5;"></i>
                                    <p class="text-muted mb-0 font-weight-bold">Bạn đã đánh giá tất cả đơn hàng</p>
                                    <small class="text-muted">Cảm ơn bạn đã đóng góp ý kiến!</small>
                                </div>
                            @endif
                        @endguest
                    </div>
                </div>

                <!-- Danh sách bình luận -->
                <div class="comments-list mt-4 pt-3 border-top">
                    @forelse($product->reviews as $review)
                        <div class="d-flex mb-4">
                            <!-- Avatar -->
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
                                            <span class="text-muted" style="font-size: 0.75rem; opacity: 0.8;">
                                                | Phân loại: {{ $review->variant_name ?? 'Mặc định' }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- THỜI GIAN, LIKE & NÚT SỬA/XÓA -->
                                    <div class="d-flex align-items-center">
                                        <!-- Nút Thích Bình Luận -->
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0 mr-3 btn-like-review" data-id="{{ $review->id }}" style="text-decoration: none; box-shadow: none;">
                                            <i class="{{ $review->isLikedByAuthUser() ? 'fas text-primary' : 'far' }} fa-thumbs-up icon-like"></i>
                                            <span class="text-like {{ $review->isLikedByAuthUser() ? 'text-primary font-weight-bold' : '' }}">Hữu ích</span>
                                            (<span class="like-count">{{ $review->likesCount() }}</span>)
                                        </button>

                                        <small class="text-muted ml-2 text-nowrap">{{ $review->created_at->diffForHumans() }}</small>
                                        
                                        <!-- Nút Sửa / Xóa -->
                                        @if(Auth::check() && Auth::id() == $review->user_id)
                                            <div class="dropdown ml-2">
                                                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-toggle="dropdown" style="box-shadow: none;">
                                                    <i class="fas fa-ellipsis-v px-2"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                    <button type="button" class="dropdown-item" data-toggle="modal" data-target="#editReviewModal-{{ $review->id }}">
                                                        <i class="fas fa-edit mr-2 text-primary"></i> Sửa đánh giá
                                                    </button>
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('review.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash-alt mr-2"></i> Xóa đánh giá
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Modal Sửa Đánh Giá -->
                                            <div class="modal fade" id="editReviewModal-{{ $review->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content border-0 shadow">
                                                        <form action="{{ route('review.update', $review->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header bg-light border-0">
                                                                <h5 class="modal-title font-weight-bold">Sửa đánh giá</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="star-rating mb-3 justify-content-center">
                                                                    @for($star = 5; $star >= 1; $star--)
                                                                        <input type="radio" id="edit_star{{ $star }}_{{ $review->id }}" name="rating" value="{{ $star }}" {{ $review->rating == $star ? 'checked' : '' }}>
                                                                        <label for="edit_star{{ $star }}_{{ $review->id }}" title="{{ $star }} sao"><i class="fas fa-star" style="font-size: 2rem;"></i></label>
                                                                    @endfor
                                                                </div>
                                                                <textarea class="form-control shadow-sm" name="comment" rows="3" required style="resize: none;">{{ $review->comment }}</textarea>
                                                            </div>
                                                            <div class="modal-footer border-0 bg-light">
                                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Lưu thay đổi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
                                
                                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                    {{ $review->comment }}
                                </p>

                                @if($review->admin_reply)
                                    <div class="admin-reply-box mt-3 p-3 rounded shadow-sm" style="background-color: #f4f6f9; border-left: 4px solid var(--primary-color);">
                                        <div class="d-flex align-items-center mb-2">
                                            <!-- Icon Admin -->
                                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm mr-2" 
                                                style="width: 32px; height: 32px; background-color: var(--primary-color);">
                                                <i class="fas fa-headset" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-weight-bold" style="color: var(--primary-color);">Phản hồi từ Shop</h6>
                                            </div>
                                        </div>
                                        <!-- Nội dung trả lời -->
                                        <div class="text-dark ml-4 pl-1" style="font-size: 0.95rem; line-height: 1.6;">
                                            {!! nl2br(e($review->admin_reply)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr class="text-muted" style="opacity: 0.1">
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="far fa-comments fa-3x mb-3" style="color: #dee2e6;"></i>
                            <p>Chưa có đánh giá nào. Hãy là người đầu tiên nhận xét về cuốn sách này!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sách cùng danh mục --}}
        <div class="mb-5 bg-white p-4 rounded shadow-sm border mt-4">
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                <h2 class="serif-font font-weight-bold mb-0">Sách của tác giả {{ $product->author->name }}</h2>
                <a href="{{ route('user.author', $product->author_id) }}" class="text-muted text-decoration-none">Xem tất cả</a>
            </div>

            @if($relatedProducts->isEmpty())
                <div class="text-center py-5 text-muted">Chưa có sách cùng danh mục.</div>
            @else
                <div class="row">
                    @foreach($relatedProducts as $item)
                        <div class="col-lg-2 col-md-3 col-6 mb-4">
                            <div class="card border-0 h-100 text-center">
                                <a href="{{ route('user.productDetails',$item->id) }}" class="d-flex justify-content-center mt-2">
                                    <img src="{{ asset('uploads/products/'.$item->image) }}" class="rounded shadow" style="width:120px; height:180px; object-fit:cover;" alt="{{ $item->name }}">
                                </a>
                                <div class="card-body p-2">
                                    <h6 class="mb-2 text-truncate">{{ $item->name }}</h6>
                                    <div class="font-weight-bold" style="color:#D35400;">{{ number_format($item->price,0,',','.') }} ₫</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .chon-phien-ban input { display: none; }
    .hop-phien-ban { display: block; min-width: 150px; padding: 12px 15px; border: 2px solid #EEEEEE; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; background: #fff; }
    .chon-phien-ban input:checked + .hop-phien-ban { border: 2px solid var(--primary-color); background: #FFF6F0; }
    .chon-phien-ban input:disabled + .hop-phien-ban { opacity: 0.5; background: #F8F9FA; cursor: not-allowed; }
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
    .star-rating input { display: none; }
    .star-rating label { color: #ddd; font-size: 1.5rem; padding: 0 0.1rem; cursor: pointer; transition: color 0.2s; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right", "timeOut": "2500" };

    // Yêu thích
    document.querySelector('.btn-wishlist').addEventListener('click', function(e) {
        e.preventDefault();
        let btn = this, productId = btn.getAttribute('data-id'), icon = btn.querySelector('i');
        axios.post('{{ route('user.wishlist.toggle') }}', { product_id: productId, _token: '{{ csrf_token() }}' })
        .then(res => {
            if(res.data.status === 'added') { icon.classList.replace('far', 'fas'); toastr.success(res.data.message); }
            else { icon.classList.replace('fas', 'far'); toastr.info(res.data.message); }
        }).catch(err => err.response?.status === 401 ? toastr.warning("Đăng nhập để thêm vào yêu thích!") : toastr.error("Lỗi!"));
    });

    // Giá / Tồn kho
    const danhSachRadio = document.querySelectorAll('input[name="product_variant_id"]');
    const oHienThiGia = document.getElementById('hien-thi-gia'), oSoLuong = document.getElementById('o-so-luong'), oForm = document.getElementById('them-vao-gio-hang');
    let tonKhoHienTai = document.querySelector('input[name="product_variant_id"]:checked')?.dataset.tonKho || 0;

    danhSachRadio.forEach(r => r.addEventListener('change', function () {
        tonKhoHienTai = parseInt(this.dataset.tonKho);
        oHienThiGia.innerHTML = parseInt(this.dataset.gia).toLocaleString('vi-VN') + ' VNĐ';
        oSoLuong.value = 1;
    }));

    oSoLuong.addEventListener('input', function () {
        let v = parseInt(this.value);
        if(v < 1 || isNaN(v)) this.value = 1;
        if(tonKhoHienTai > 0 && v > tonKhoHienTai) this.value = tonKhoHienTai;
    });

    oForm.addEventListener('submit', function (e) {
        if(!document.querySelector('input[name="product_variant_id"]:checked')) { e.preventDefault(); alert('Vui lòng chọn phiên bản!'); }
    });

    // Thích bình luận
    document.querySelectorAll('.btn-like-review').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let id = this.getAttribute('data-id'), icon = this.querySelector('.icon-like'), text = this.querySelector('.text-like'), countSpan = this.querySelector('.like-count');
            axios.post('/reviews/' + id + '/like', { _token: '{{ csrf_token() }}' })
            .then(res => {
                countSpan.innerText = res.data.likesCount;
                if(res.data.status === 'liked') {
                    icon.classList.replace('far', 'fas'); icon.classList.add('text-primary'); text.classList.add('text-primary', 'font-weight-bold');
                } else {
                    icon.classList.replace('fas', 'far'); icon.classList.remove('text-primary'); text.classList.remove('text-primary', 'font-weight-bold');
                }
            }).catch(err => err.response?.status === 401 ? toastr.warning("Đăng nhập để thích bình luận!") : toastr.error("Lỗi!"));
        });
    });
});
</script>
@endpush