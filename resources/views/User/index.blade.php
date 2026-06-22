@include('User.header')

    <section class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="hero__categories mb-4">
                        <div class="hero__categories__all">
                            <i class="fa fa-bars"></i>
                            <span>TẤT CẢ DANH MỤC</span>
                        </div>
                        <ul>
                            @foreach ($categories as $category)
                                <li><a href="#">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card border-0 shadow-sm mt-4" style="background: #fdfdfd; border-radius: 4px; border: 1px solid #ececec !important;">
                        <div class="card-header text-white font-weight-bold" style="background-color: #7fad39; border-radius: 4px 4px 0 0; padding: 12px 15px; font-size: 14px;">
                            <i class="fa fa-filter"></i> BỘ LỌC TÌM KIẾM
                        </div>
                        <div class="card-body" style="padding: 20px 15px;">
                            <form action="{{ route('user.index') }}" method="GET">
                                
                                <div class="filter-section mb-4">
                                    <h6 class="font-weight-bold text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px; margin-bottom: 10px;">Khoảng Giá (VND)</h6>
                                    <div class="d-flex align-items-center" style="gap: 6px; display: flex;">
                                        <input type="number" name="price_min" value="{{ request('price_min') }}" class="form-control form-control-sm text-center" placeholder="Từ" style="border-radius: 4px; font-size: 13px; height: 35px; width: 45%; text-align: center; border: 1px solid #ced4da;">
                                        <span class="text-muted" style="margin: 0 4px;">-</span>
                                        <input type="number" name="price_max" value="{{ request('price_max') }}" class="form-control form-control-sm text-center" placeholder="Đến" style="border-radius: 4px; font-size: 13px; height: 35px; width: 45%; text-align: center; border: 1px solid #ced4da;">
                                    </div>
                                </div>

                                <hr style="border-top: 1px dashed #ddd; margin: 15px 0;">

                                <div class="filter-section mb-4">
                                    <h6 class="font-weight-bold text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px; margin-bottom: 10px;">Tác Giả</h6>
                                    <select name="author" class="form-control form-control-sm" style="border-radius: 4px; cursor: pointer; font-size: 13px; height: 35px; width: 100%; border: 1px solid #ced4da;">
                                        <option value="">-- Chọn Tác Giả --</option>
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                                {{ $author->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr style="border-top: 1px dashed #ddd; margin: 15px 0;">

                                <div class="filter-section mb-4">
                                    <h6 class="font-weight-bold text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px; margin-bottom: 10px;">Nhà Xuất Bản</h6>
                                    <select name="publisher" class="form-control form-control-sm" style="border-radius: 4px; cursor: pointer; font-size: 13px; height: 35px; width: 100%; border: 1px solid #ced4da;">
                                        <option value="">-- Chọn Nhà Xuất Bản --</option>
                                        @foreach($publishers as $pub)
                                            <option value="{{ $pub->id }}" {{ request('publisher') == $pub->id ? 'selected' : '' }}>
                                                {{ $pub->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn text-white font-weight-bold w-100 btn-sm mb-2" style="background-color: #7fad39; border-radius: 4px; border: none; padding: 10px; font-size: 13px; letter-spacing: 0.5px; width: 100%; cursor: pointer;">
                                        ÁP DỤNG BỘ LỌC
                                    </button>
                                    
                                    @if(request()->filled('price_min') || request()->filled('price_max') || request()->filled('publisher') || request()->filled('author'))
                                        <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm w-100 font-weight-bold" style="border-radius: 4px; padding: 8px; font-size: 13px; display: block; text-align: center; background-color: #6c757d; color: white; text-decoration: none; margin-top: 5px;">
                                            XÓA BỘ LỌC
                                        </a>
                                    @endif
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="hero__search">
                        <div class="hero__search__form">
                            <form action="{{ route('user.search') }}" method="GET">
                                <input 
                                    type="text"
                                    name="keyword"
                                    placeholder="Tìm kiếm sản phẩm ..."
                                    value="{{ request('keyword') }}"
                                >
                                <button type="submit" class="site-btn">TÌM KIẾM</button>
                            </form>
                        </div>
                        <div class="hero__search__phone">
                            <div class="hero__search__phone__icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="hero__search__phone__text">
                                <h5>+65 11.188.888</h5>
                                <span>support 24/7 time</span>
                            </div>
                        </div>
                    </div>
                    <div class="hero__item set-bg" data-setbg="img/hero/banner.jpg">
                        <div class="hero__text">
                            <span>FRUIT FRESH</span>
                            <h2>Vegetable <br />100% Organic</h2>
                            <p>Free Pickup and Delivery Available</p>
                            <a href="#" class="primary-btn">SHOP NOW</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="categories">
        <div class="container">
            <div class="row">
                <div class="categories__slider owl-carousel">
                     @foreach ($categories as $category)
                    <div class="col-lg-3">
                        <div class="categories__item set-bg" data-setbg="{{ asset('uploads/categories/' . $category->image) }}">
                            <h5><a href="#">{{ $category->name }}</a></h5>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="featured spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>Featured Product</h2>
                    </div>
                </div>
            </div>
            
            <div class="row">
                @if($products->isEmpty())
                    <div class="col-lg-12 text-center mt-4 mb-4">
                        <h4 class="text-muted">Không tìm thấy sản phẩm nào phù hợp với bộ lọc!</h4>
                    </div>
                @else
                    @foreach ($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4"> 
                            <div class="featured__item">
                                <div class="featured__item__pic set-bg" data-setbg="{{ asset('uploads/products/' . $product->image) }}">
                                    <ul class="featured__item__pic__hover">
                                        <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                        <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                        <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="featured__item__text">
                                    <h6><a href="{{ route('user.productDetails', $product->id) }}">{{ $product->name }}</a></h6>
                                    <h5>{{ number_format($product->price, 0, ',', '.') }} VND</h5>
                                </div>
                            </div>
                        </div>
                   @endforeach
                @endif
            </div>
        </div>
    </section>

    <div class="banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="banner__pic">
                        <img src="img/banner/banner-1.jpg" alt="">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="banner__pic">
                        <img src="img/banner/banner-2.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="latest-product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>Top 10 sản phẩm mới</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="img/latest-product/lp-1.jpg" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Crab Pool Security</h6>
                                        <span>$30.00</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>Top 10 sản phẩm bán chạy</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="img/latest-product/lp-1.jpg" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Crab Pool Security</h6>
                                        <span>$30.00</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>Top 10 sản phẩm đánh giá cao</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="img/latest-product/lp-1.jpg" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Crab Pool Security</h6>
                                        <span>$30.00</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="from-blog spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title from-blog__title">
                        <h2>From The Blog</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="img/blog/blog-1.jpg" alt="">
                        </div>
                        <div class="blog__item__text">
                            <ul>
                                <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                                <li><i class="fa fa-comment-o"></i> 5</li>
                            </ul>
                            <h5><a href="#">Cooking tips make cooking simple</a></h5>
                            <p>Sed quia non numquam modi tempora indunt ut labore et dolore magnam aliquam quaerat </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="img/blog/blog-2.jpg" alt="">
                        </div>
                        <div class="blog__item__text">
                            <ul>
                                <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                                <li><i class="fa fa-comment-o"></i> 5</li>
                            </ul>
                            <h5><a href="#">6 ways to prepare breakfast for 30</a></h5>
                            <p>Sed quia non numquam modi tempora indunt ut labore et dolore magnam aliquam quaerat </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="img/blog/blog-3.jpg" alt="">
                        </div>
                        <div class="blog__item__text">
                            <ul>
                                <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                                <li><i class="fa fa-comment-o"></i> 5</li>
                            </ul>
                            <h5><a href="#">Visit the clean farm in the US</a></h5>
                            <p>Sed quia non numquam modi tempora indunt ut labore et dolore magnam aliquam quaerat </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('User.footer')