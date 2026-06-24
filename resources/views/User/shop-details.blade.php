@include('User.header')

<section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>Trang Chi Tiết Sản Phẩm</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="product-details spad">
    <div class="container">
        {{-- Hiển thị thông báo lỗi/thành công --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">Vui lòng chọn phiên bản sản phẩm!</div>
        @endif

        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__pic">
                        <div class="product__details__pic__item">
                            <img class="product__details__pic__item--large"
                                src="{{ asset('uploads/products/' . $product->image) }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__text">
                        <h3>{{ $product->name }}</h3>
                        <div class="product__details__rating">
                            <i class="fa fa-star"></i>
                            <span>(18 reviews)</span>
                        </div>
                        <div class="product__details__price">
                            <span id="product-price">{{ number_format($product->price) }} VNĐ</span>
                        </div>
                        <p>{{ $product->description }}</p>

                        <div class="product__details__quantity">
                            <div class="quantity">
                                <div class="pro-qty">
                                    <input type="number" name="quantity" value="1" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Phiên bản</h5>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($product->variants as $variant)
                                    @if($variant->price > 0)
                                        <label class="variant-item">
                                            <input type="radio" name="variant_id" value="{{ $variant->id }}" 
                                                data-price="{{ $variant->price }}" 
                                                {{ $variant->stock <= 0 ? 'disabled' : '' }} required>
                                            <span class="variant-box">
                                                <strong>{{ $variant->edition }}</strong><br>
                                                <small>Giá: {{ number_format($variant->price) }} VNĐ</small><br>
                                                <small class="{{ $variant->stock > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $variant->stock > 0 ? 'Còn ' . $variant->stock : 'Hết hàng' }}
                                                </small>
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="primary-btn mt-4" style="border:none;">Thêm vào giỏ hàng</button>
                        <a href="#" class="heart-icon"><span class="icon_heart_alt"></span></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
    .variant-item input { display:none; }
    .variant-box { display:block; min-width:140px; padding:12px; border:1px solid #ddd; border-radius:8px; cursor:pointer; text-align:center; }
    .variant-item input:checked + .variant-box { border:2px solid #7fad39; background:#f6fff0; }
    .variant-item input:disabled + .variant-box { opacity:0.5; cursor:not-allowed; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="variant_id"]');
        const priceElement = document.getElementById('product-price');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                let price = parseInt(this.dataset.price);
                priceElement.innerHTML = price.toLocaleString('vi-VN') + ' VNĐ';
            });
        });
    });
</script>

@include('User.footer')