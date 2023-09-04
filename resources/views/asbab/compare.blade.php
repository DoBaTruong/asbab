@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/product/product.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/wishlist/wishlist.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/wishlist/wishlist.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="{{ route('asbab.home') }}">Home</a>
                        <span class="breadcrumb-item active">Compare</span>
                    </nav>
                </div>
            </div>
        </section>
        <section class="wishlist-area">
            <div class="container">
                <div class="row" id="compare-table" data-url="{{ route('asbab.compare.data') }}"></div>
                <div class="row d-flex justify-content-end mt-2 mb-5">
                    <ul class="pagination" id="pagination"></ul>
                </div>
            </div>
        </section>
        <section class="slide-brand-area">
            <div class="container">
                <ul class="row owl-carousel owl-brand">
                    @foreach ($brands as $brand)
                        <li class="brand-logo"><a href="{{ $brand->link }}"><img src="{{ asset($brand->image_path) }}"
                                    alt="{{ $brand->name }}" /></a></li>
                    @endforeach
                </ul>
            </div>
        </section>
    </main>
@endsection
