<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SachHay</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght=200;300;400;600;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/jquery-ui.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('ogani-1.0.0/css/style.css') }}" type="text/css">
</head>

<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <div class="humberger__menu__overlay"></div>
    <div class="humberger__menu__wrapper">
        <div class="humberger__menu__logo">
            <a href="#"><img src="img/logo.png" alt=""></a>
        </div>
        <div class="humberger__menu__cart">
            <ul>
                <li><a href="#"><i class="fa fa-heart"></i> </a></li>
                <li><a href="#"><i class="fa fa-shopping-bag"></i> </a></li>
            </ul>
            <div class="header__cart__price"></div>
        </div>
        
        <div class="humberger__menu__widget">
            <div class="header__top__right__auth">
                @auth
                    <a href="{{ route('profile.edit') }}" style="font-size: 14px; color: #333; text-decoration: none; font-weight: bold;">
                        <i class="fa fa-user"></i> Xin chào, <span style="color: #7fad39;">{{ Auth::user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"><i class="fa fa-user"></i> Đăng nhập</a>
                @endauth
            </div>
        </div>

        <nav class="humberger__menu__nav mobile-menu">
            <ul>
                <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}"><a href="{{ route('user.index') }}">Home</a></li>
                <li class="{{ request()->routeIs('user.shop') ? 'active' : '' }}"><a href="{{ route('user.shop') }}">Shop</a></li>
                <li>
                    <a href="#">Pages</a>
                    <ul class="header__menu__dropdown">
                        <li><a href="">Shop Details</a></li>
                    </ul>
                </li>
                <li><a href="">Blog</a></li>
                <li><a href="">Contact</a></li>
            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
    </div>
    
    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="header__top__left">
                            <ul>
                                <li><i class="fa fa-envelope"></i> hello@colorlib.com</li>
                                <li>Miễn phí ship cho đơn hàng trên 100k</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="header__top__right">
                            <div class="header__top__right__social">
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-linkedin"></i></a>
                                <a href="#"><i class="fa fa-pinterest-p"></i></a>
                            </div>
                           
                            <div class="header__top__right__auth">
                                @auth
                                    <a href="{{ route('profile.edit') }}" class="user-header-btn" style="text-decoration: none; color: #333; font-size: 14px; font-weight: 500; display: inline-block; transition: all 0.2s;">
                                        <i class="fa fa-user" style="margin-right: 4px;"></i> Xin chào, <strong style="color: #7fad39; border-bottom: 1px dashed #7fad39;">{{ Auth::user()->name }}</strong>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" style="text-decoration: none; color: #333; font-weight: 500;">
                                        <i class="fa fa-user"></i> Đăng nhập
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="header__logo">
                        <a href="./index.html"><img src="img/logo.png" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="header__menu">
                        <ul>
                            <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}"><a href="{{ route('user.index') }}">Home</a></li>
                            <li class="{{ request()->routeIs('user.shop') ? 'active' : '' }}"><a href="{{ route('user.shop') }}">Shop</a></li>
                            <li>
                                <a href="#">Pages</a>
                                <ul class="header__menu__dropdown">
                                    <li><a href="">Shop Details</a></li>
                                </ul>
                            </li>
                            <li><a href="">Blog</a></li>
                            <li><a href="">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="header__cart">
                        <ul>
                            <li><a href="/cart"><i class="fa fa-shopping-bag"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="humberger__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <style>
        .user-header-btn:hover { opacity: 0.8; }
        .user-header-btn:hover strong { color: #6a9230 !important; }
    </style>

    <script src="{{ asset('ogani-1.0.0/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('ogani-1.0.0/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('ogani-1.0.0/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('ogani-1.0.0/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('ogani-1.0.0/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('ogani-1.0.0/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('ogani-1.0.0/js/main.js') }}"></script>
</body>
</html>