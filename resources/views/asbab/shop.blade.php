@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/product/product.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/assets/infinite-ajax-scroll/infinite-ajax-scroll.min.js') }}"></script>
    <script src="{{ asset('guest/assets/animateJS/anime.min.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/product/product.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="index.html">Home</a>
                        <span class="breadcrumb-item active">Products</span>
                    </nav>
                </div>
            </div>
        </section>
        <section class="prd-grids">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-12" id="infinite-scroll">
                        <div class="prd-right-top mb-4">
                            <div class="grid-select-option">
                                <select class="grid-select">
                                    <option value="0">Default softing</option>
                                    <option value="1">Sort by name</option>
                                    <option value="2">Sort by popularity</option>
                                    <option value="3">Sort by average rating</option>
                                    <option value="4">Sort by price</option>
                                </select>
                            </div>
                            <div class="grid-pro-show">
                                <span>Showing <span class="result-to">0</span> - <span class="result-from">0</span> of <span
                                        class="total-results">0</span> products</span>
                            </div>
                            <ul class="view-mode" id="nav-view">
                                <li class="grid-view active"><a href="#grid-view"><i class="fa fa-th"></i></a></li>
                                <li class="list-view ml-2"><a href="#list-view"><i class="fa fa-th-list"></i></a></li>
                            </ul>
                        </div>
                        <div class="row product-list product-list-view fade" id="list-view"
                            data-url="{{ route('asbab.shop.data') }}">
                        </div>
                        <div class="row product-list product-grid-view" id="grid-view">
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="spinner">
                                <div class="line">
                                    <div class="circle-el"></div>
                                </div>
                                <div class="line">
                                    <div class="circle-el"></div>
                                </div>
                                <div class="line">
                                    <div class="circle-el"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="prd-grid-range">
                            <h4>Price</h4>
                            <div class="price-filter s-filter">
                                <form method="GET">
                                    @csrf
                                    <div id="slider-range"></div>
                                    <div class="slider-range-output mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="price-output">
                                                <span>Price:</span><input type="text" id="amount" readonly />
                                            </div>
                                            <div class="btn-filter">
                                                <a href="#" class="submit-filter">Filter</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="widget-categories mt-5">
                            <h4>categories</h4>
                            <ul class="cat-list">
                                @foreach ($categories as $category)
                                    <li><a data-info="{{ $category->id }}" class="cat-item">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="widget-tags mt-5">
                            <h4>tags</h4>
                            <ul class="tag-list">
                                @foreach ($tags as $tag)
                                    <li><a class="tag-item" data-info="{{ $tag->id }}">{{ $tag->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="widget-compare mt-5">
                            <h4>compare</h4>
                            <ul class="compare-list"></ul>
                        </div>
                        <div class="widget-recent mt-5">
                            <h4>best seller</h4>
                            <div class="recent-product-inner">
                                @foreach ($seller as $sell)
                                    <div class="product-item">
                                        <div class="prd-item-thumb">
                                            <a><img src="{{ asset($sell->feature_image_path) }}" alt="{{ $sell->slug }}" /></a>
                                        </div>
                                        <div class="prd-item-infor">
                                            <div class="infor-content">
                                                <a class="mb-2 product-name" data-info="{{ json_encode(['id' => $sell->id, 'price' => number_format($sell->price, 2, ',', '.')]) }}" href="{{ route('asbab.product.show', ['slug' => $sell->slug]) }}">{{ $sell->name }}</a>
                                                <div class="infor-rating" data-stars="{{ $sell->rates->avg('rate') / 0.05 . '%' }}">
                                                    <span class="stars ml-0"><i class="far fa-star"></i><i
                                                            class="far fa-star"></i><i class="far fa-star"></i><i
                                                            class="far fa-star"></i><i class="far fa-star"></i></span>
                                                </div>
                                                <p class="infor-price"><span class="old-price"></span>${{ number_format($sell->price, 2, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="slide-brand-area">
            <div class="container">
                <ul class="row owl-carousel owl-brand">
                    @foreach ($brands as $brand)
                        <li><a href="{{ $brand->link }}"><img src="{{ asset($brand->image_path) }}"
                                    alt="{{ $brand->name }}" /></a></li>
                    @endforeach
                </ul>
            </div>
        </section>
    </main>
@endsection
