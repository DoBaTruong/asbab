<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Asbab | Furniture</title>

    <link rel="shortcut icon" href="{{ asset('guest/images/logo/favicon.ico') }}" type="image/x-icon"/>
    <link rel="stylesheet" href="{{ asset('guest/assets/bootstrap/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('guest/css/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('guest/css/animate.css') }}"/>
    <link rel="stylesheet" href="{{ asset('guest/css/common.css') }}"/>
    @yield('css')
    <link rel="stylesheet" href="{{ asset('guest/css/responsive.css') }}"/>
</head>

<body data-url_base="{{ route('asbab.home') }}">
<header id="header-area">
    <section class="top-header">
        <div class="container">
            <div class="row">
                <div class="col-4 col-lg-2 col-md-2 col-sm-3">
                    <a class="logo-header" href="#">
                        <img class="w-100" src="{{ asset('guest/images/logo/logo.png') }}"
                             alt="logo images"/>
                    </a>
                </div>
                <div class="col-2 col-lg-8 col-md-6 col-sm-3">
                    <nav class="main-menu d-none d-lg-block">
                        <ul class="nav">
                            <li><a class="nav-link" href="{{ route('asbab.home') }}">Home</a></li>
                            <li><a class="nav-link" href="{{ route('asbab.shop.index') }}">Shop</a></li>
                            <li><a class="nav-link" href="{{ route('asbab.news.index') }}">Blog</a></li>
                            <li><a class="nav-link" href="{{ route('asbab.contact.index') }}">Contact</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="col-4 col-lg-2 col-md-3 col-sm-4">
                    <div class="top-head-right">
                        <span class="search-btn-open border-right"><i class="fa fa-search"></i></span>
                        <span class="btn-header-account @if (!auth()->user()) btn-open-login @endif">
                                @if (auth()->user())
                                <span class="btn-account-select"><i class="far fa-user"></i></span>
                                <div id="option-account">
                                        <span class="option-comtainer">
                                            <a href="{{ route('asbab.account.index') }}" class="mb-1"><i
                                                    class="fa fa-briefcase mr-2"></i> My Account</a>
                                            <a href="{{ route('asbab.logout') }}"><i class="fa fa-unlock mr-2"></i>
                                                Logout</a>
                                        </span>
                                    </div>
                            @else
                                <span data-toggle="tooltip" title="Log In"><i class="fa fa-key"></i></span>
                            @endif
                            </span>
                        <span class="border-left cart-btn-open"><i class="fa fa-shopping-bag"></i>
                                @php
                                    $item = 0;
                                    if($carts && count($carts) > 0) {
                                        foreach($carts as $cart) {
                                            $item += $cart['quantity'];
                                        }
                                    }
                                @endphp
                                <span class="badge">{{ $item }}</span></span>
                    </div>
                </div>
                <div class="col-2 col-md-1 col-sm-2 d-lg-none mobile-icon"><span><i
                            class="fa fa-bars"></i></span>
                </div>
            </div>
        </div>
    </section>
    <section id="login_account">
        <form data-action="{{ route('asbab.login') }}" method="post" class="login-form">
            @csrf
            <h4 class="text-center">LOGIN</h4>
            <span class="btn-close-login"><i class="fa fa-times"></i></span>
            <div class="form-group">
                <label>Email:</label>
                <input type="text" name="email" class="form-control"/>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control"/>
            </div>
            <div class="form-group d-flex justify-content-between">
                <label>
                    <input type="checkbox" name="remember_me"/>
                    Remember me
                </label>
                <a href="#">Forget Password ?</a>
            </div>
            <div class="btn-login-group">
                <button type="submit" class="btn btn-login-account">Log In</button>
                <div class="login-socials">
                    <h5>or login with</h5>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('asbab.login.redirect', ['provider' => 'facebook']) }}"
                           class="bg-primary"><i class="fab fa-facebook-f mr-1"></i> Facebook</a>
                        <a href="{{ route('asbab.login.redirect', ['provider' => 'google']) }}"
                           class="bg-danger"><i class="fab fa-google-plus-g mr-1"></i> Google+</a>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <section class="mobile-menu d-block d-lg-none">
        <div class="container">
            <div class="row">
                <nav id="mobile-menu">
                    <ul class="nav">
                        <li><a class="nav-link" href="{{ route('asbab.home') }}">Home</a></li>
                        <li><a class="nav-link" href="{{ route('asbab.shop.index') }}">Shop</a></li>
                        <li><a class="nav-link" href="{{ route('asbab.news.index') }}">Blog</a></li>
                        <li><a class="nav-link" href="{{ route('asbab.contact.index') }}">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
    <div class="body-overlay"></div>
    <section class="search-area">
        <div class="container">
            <div class="row">
                <div class="search-inner">
                    <form method="get" id="search-enter" data-action="{{ route('asbab.home') }}">
                        @csrf
                        <input type="search" name="keyword" placeholder="Search here..."/>
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                    <div class="search-btn-close"><i class="fa fa-times"></i></div>
                </div>
            </div>
            <section id="search-results" class="row"></section>
            <section id="search-pagination" class="row d-flex justify-content-end">
                <ul class="pagination"></ul>
            </section>
        </div>
    </section>
    <section class="shopping-cart">
        <div class="shopping-cart-inner">
            <div class="cart-btn-close"><i class="fa fa-times"></i></div>
            <div class="shopping-cart-wrap">
                @php
                    $total = 0;
                @endphp
                @if (!empty($carts))
                    @foreach ($carts as $cart)
                        @php
                            $total += $cart['price'] * $cart['quantity'];
                        @endphp
                        <div class="shp-single-prd cart-product-item">
                            <div class="shp-prd-infor d-flex">
                                <div class="shp-prd-thumb">
                                    <a href="#"><img src="{{ asset($cart['image_path']) }}" alt=""/></a>
                                </div>
                                <div class="shp-prd-details">
                                    <a href="#">{{ $cart['name'] }}</a>
                                    <span class="quantity">QTY: {{ $cart['quantity'] }}</span>
                                    <span
                                        class="shp-price">${{ number_format($cart['price'], 2, '.', ',') }}</span>
                                </div>
                            </div>
                            <div class="prd-btn-remove">
                                <a data-href="{{ route('asbab.cart.removecart', ['id' => $cart['id']]) }}"
                                   class="btn-remove-cart"><i class="fa fa-times"></i></a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="shp-single-prd cart-product-item text-center">No have product on cart !</div>
                @endif
            </div>
            <div class="shp-cart-total d-flex justify-content-between">
                <span class="subtotal">Subtotal:</span>
                <span class="total-price">${{ number_format($total, 2, ',', '.') }}</span>
            </div>
            <ul class="shp-btn-foot p-0">
                <li class="shp-viewcart"><a href="{{ route('asbab.cart.index') }}">View Cart</a></li>
            </ul>
        </div>
    </section>
</header>
@yield('content')
<footer>
    <section class="footer-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <h2>ABOUT US</h2>
                    <div class="ft-about">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore magna aliqua. Ut enim</p>
                        <ul class="ft-social-link">
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-google"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h2>Information</h2>
                    <div class="ft-inner">
                        <ul class="ft-list">
                            <li><a href="#">About us</a></li>
                            <li><a href="#">Delivery Information</a></li>
                            <li><a href="#">Privacy & Policy</a></li>
                            <li><a href="#">Terms & Condition</a></li>
                            <li><a href="#">Manufactures</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h2>Our service</h2>
                    <div class="ft-inner">
                        <ul class="ft-list">
                            @if (auth()->user())
                                <li><a href="{{ route('asbab.account.index') }}">My Account</a></li>
                            @else
                                <li><a href="#" class="btn-open-login">My Account</a></li>
                            @endif
                            <li><a href="{{ route('asbab.cart.index') }}">My Cart</a></li>
                            @if (!auth()->user())
                                <li><a href="#" class="btn-open-login">Login</a></li>
                            @endif
                            <li><a href="{{ route('asbab.wishlist') }}">Wishlist</a></li>
                            <li><a href="{{ route('asbab.cart.index') . '#checkout-area' }}">Checkout</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 xmt-40 smt-40">
                    <h2>NEWSLETTER </h2>
                    <div class="ft-inner">
                        <div class="news-input">
                            <input type="text" placeholder="Your Mail*">
                            <div class="send-btn">
                                <a class="fr-btn" href="#">Send Mail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="ft-copyright">
        <div class="container">
            <div class="row">
                <div class="copyright-inner">
                    <p>Copyright© <a href="#">BaTruong</a> 2021. All right reserved.</p>
                </div>
            </div>
        </div>
    </section>
</footer>

<section id="chat-with-shop" data-url="{{ route('asbab.chat.index') }}">
    <div class="chat-title">
        <span class="chat-title-text">CHAT WITH SHOP</span>
        <span class="chat-title-icon"></span>
    </div>
    <div class="chat-wrap">
        <ul class="chat-meassage"></ul>
        <form id="chat-inbox" data-action="{{ route('asbab.chat.store') }}" class="chat-enter-content d-flex">
            <input class="form-control" type="text" name="message" placeholder="Write message..."/>
            <button class="btn btn-small btn-info" type="submit"><i class="fa fa-paper-plane"></i></button>
        </form>
    </div>
</section>

<script src="{{ asset('guest/assets/jquery/jquery-3.5.0.min.js') }}"></script>
<script src="{{ asset('guest/assets/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
<script>
    var pusher = new Pusher('af88ad31025c923bf4f8', {
        forceTLS: true,
        cluster: 'ap1'
    });
    var channel = pusher.subscribe('chat-with-admin');
    channel.bind('chat-admin', function (data) {
        console.log(data)
        $(`<li class="message-item replies">
                    <span class="mess-img">
                        <img src="${'https://asbab.dev.com/' + data.user}" alt="">
                    </span>
                    <p>${data.message}</p>
                </li>`).appendTo('#chat-with-shop .chat-wrap .chat-meassage');
    });
</script>

<script src="{{ asset('guest/js/plugins.js') }}"></script>
<script src="{{ asset('guest/js/active.js') }}"></script>
@yield('js')
</body>

</html>
