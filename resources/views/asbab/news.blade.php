@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/blog/blog.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/assets/infinite-ajax-scroll/infinite-ajax-scroll.min.js') }}"></script>
    <script src="{{ asset('guest/assets/animateJS/anime.min.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/blog/blog.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="{{ route('asbab.home') }}">Home</a>
                        <span class="breadcrumb-item active">News</span>
                    </nav>
                </div>
            </div>
        </section>
        <section class="blog-container-area">
            <div class="container">
                <div class="row blog-list" id="infinite-news" data-url="{{ route('asbab.news.data') }}"></div>
                <div class="row d-flex justify-content-center">
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
