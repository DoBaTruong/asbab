@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}"/>
    <link rel="stylesheet" href="{{ asset('guest/product/product.css') }}"/>
    <link rel="stylesheet" href="{{ asset('guest/blog/blog.css') }}"/>
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/product/product.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="owl-carousel owl-wrap">
            @foreach ($sliders as $slider)
                <div class="slide-container">
                    <div class="owl-image"><img src="{{ asset($slider->image_path) }}" alt=""/></div>
                    <div class="owl-content align-items-center d-flex">
                        <div class="text-center w-100">
                            <h2>{{ $slider->name }}</h2>
                            <h1>{{ $slider->description }}</h1>
                            <div class="cr-btn">
                                <a href="{{ route('asbab.shop.index') }}">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
        <section class="product-container-area">
            <h2 class="text-center">New Arrivals</h2>
            <div class="container">
                <div class="row product-list list-masory">
                    @foreach ($products as $product)
                        <div class="col-lg-6 col-md-6 col-sm-12 single-product">
                            <div class="product-item">
                                <div class="prd-item-thumb">
                                    <a href="#"><img src="{{ asset($product->feature_image_path) }}" alt=""/></a>
                                </div>
                                <ul class="prd-item-action">
                                    <li><a class="btn-add-wishlist" href="#"
                                           data-info="{{ json_encode(['id' => $product->id]) }}"><i
                                                class="far fa-heart"></i></a></li>
                                    <li><a href="#" class="btn-add-cart"
                                           data-url="{{ route('asbab.cart.addcart', ['id' => $product->id]) }}"><i
                                                class="fas fa-shopping-bag"></i></a></li>
                                    <li><a class="btn-add-compare" href="#"
                                           data-info="{{ json_encode(['id' => $product->id]) }}"><i
                                                class="fa fa-random"></i></a></li>
                                </ul>
                                <div class="prd-item-infor">
                                    <div class="infor-content">
                                        <a data-info="{{ json_encode(['id' => $product->id, 'price' => number_format($product->price, 2, ',', '.')]) }}"
                                           class="product-name"
                                           href="{{ route('asbab.product.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                                        <p class="infor-price"><span
                                                class="old-price"></span>${{ number_format($product->price, 2, '.', ',') }}
                                        </p>
                                    </div>
                                    <div class="infor-rating"
                                         data-stars="{{ $product->rates->avg('rate') / 0.05 . '%' }}">
                                        <span class="stars"><i class="far fa-star"></i><i class="far fa-star"></i><i
                                                class="far fa-star"></i><i class="far fa-star"></i><i
                                                class="far fa-star"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="prd-good-sale">
            <div class="container">
                @if($good)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="prd-prize-inner">
                                <h2>{{ $good->name }}</h2>
                                <h3>{!! trim(explode('II', explode('ABSTRACT:', strip_tags(html_entity_decode($good->details)))[1])[0]) !!}</h3>
                                <a class="prd-btn" href="{{ route('asbab.product.show', ['slug' => $good->slug]) }}">Read
                                    More</a>
                            </div>
                        </div>
                        @php
                            $detailsTable = explode('</tbody>', explode('<tbody>', explode('</table>', html_entity_decode($good->details))[0])[1])[0];
                            $trDetails = '';
                            foreach (explode('<tr', $detailsTable) as $key => $tr) {
                                if (strpos(mb_strtolower(strip_tags(html_entity_decode($tr))), 'materials')) {
                                    $trDetails = strip_tags(html_entity_decode(explode('</td>', $tr)[1]), '<br>');
                                }
                            }
                        @endphp
                        <div class="col-md-6">
                            <div class="prize-inner">
                                <div class="prize-thumb">
                                    <img class="w-100" src="{{ asset($good->feature_image_path) }}"
                                         alt="Banner images"/>
                                </div>
                                <div class="banner-infor">
                                    <div class="tooltip-box">
                                        <h4>Materials:</h4>
                                        <p>{!! $trDetails !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif;
            </div>
        </section>
        <section class="prd-best-sale">
            <h2 class="text-center">Best Seller</h2>
            <div class="container">
                <div class="row product-list">
                    @foreach ($seller as $sell)
                        <div class="col-xl-3 col-lg-4 col-md-6 single-product">
                            <div class="product-item">
                                <div class="prd-item-thumb">
                                    <a><img src="{{ asset($sell->feature_image_path) }}" alt=""/></a>
                                </div>
                                <ul class="prd-item-action">
                                    <li><a class="btn-add-wishlist" href="#"
                                           data-info="{{ json_encode(['id' => $sell->id]) }}"><i
                                                class="far fa-heart"></i></a></li>
                                    <li><a href="#" class="btn-add-cart"
                                           data-url="{{ route('asbab.cart.addcart', ['id' => $sell->id]) }}"><i
                                                class="fas fa-shopping-bag"></i></a></li>
                                    <li><a class="btn-add-compare" href="#"
                                           data-info="{{ json_encode(['id' => $sell->id]) }}"><i
                                                class="fa fa-random"></i></a></li>
                                </ul>
                                <div class="prd-item-infor">
                                    <div class="infor-content">
                                        <a data-info="{{ json_encode(['id' => $sell->id, 'price' => number_format($sell->price, 2, '.', ',')]) }}"
                                           class="product-name"
                                           href="{{ route('asbab.product.show', ['slug' => $sell->slug]) }}">{{ $sell->name }}</a>
                                        <p class="infor-price"><span
                                                class="old-price"></span>${{ number_format($sell->price, 2, '.', ',') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="slide-brand-area">
            <div class="container">
                <ul class="row owl-carousel owl-brand">
                    @foreach ($brands as $brand)
                        <li class="brand-logo"><a href="{{ $brand->link }}"><img src="{{ asset($brand->image_path) }}"
                                                                                 alt="{{ $brand->name }}"/></a></li>
                    @endforeach
                </ul>
            </div>
        </section>
        <section class="blog-container-area">
            <h2 class="text-center">Our Blog</h2>
            <div class="container">
                <div class="row blog-list">
                    @foreach ($news as $new)
                        <div class="col-xl-4 col-md-6 single-blog">
                            <div class="blog-item">
                                <div class="blog-item-thumb">
                                    <a href="#"><img src="{{ asset($new->image_path) }}" alt=""/></a>
                                </div>
                                <div class="blog-item-details">
                                    <div class="bl-date">
                                        <span>{{ date('M d, Y', strtotime($new->updated_at)) }}</span>
                                    </div>
                                    <a href="{{ route('asbab.news.details', ['slug' => $new->slug]) }}">{{ $new->title }}</a>
                                    <p class="content-details limit-line">{{ $new->abstract }}</p>
                                    <div class="blog-btn">
                                        <a href="{{ route('asbab.news.details', ['slug' => $new->slug]) }}">Read
                                            More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection
