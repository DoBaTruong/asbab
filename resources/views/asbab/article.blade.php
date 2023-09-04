@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/assets/summernote/summernote.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/blog/blog.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/assets/summernote/summernote.min.js') }}"></script>
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
                <div class="row">
                    <div class="blog-item">
                        <div class="blog-details">
                            <h4 class="mb-2"><a href="#" class="text-uppercase text-danger">{{ $blog->title }}</a>
                            </h4>
                            <div class="bl-date mb-4">
                                <span>{{ date('M d, Y', strtotime($blog->updated_at)) }}</span>
                            </div>
                            <div class="content-text text-justify">
                                {!! $blog->details !!}
                            </div>
                            <p class="bl-authors">Authors: <span>{{ $blog->authors }}</span></p>
                            <div class="bl-btn d-flex justify-content-end">
                                <a href="#" class="mr-2"><i class="fa fa-eye"></i><span class="ml-1">{{ $visits }}</span></a>
                                <a href="#" class="mr-2"><span
                                        class="{{ my_strpos($blog->likes, ',', auth()->id()) ? 'text-danger' : '' }} btn-like-comment"
                                        data-url="{{ route('asbab.news.like_comment', ['id' => $blog->id]) }}" data-type="0"><i
                                            class="{{ my_strpos($blog->likes, ',', auth()->id()) ? 'fa' : 'far' }} fa-thumbs-up"></i></span><span
                                        class="ml-1 show-like-comment">{{ $blog->likes !== null ? strcount($blog->likes, ',') : 0 }}</span></a>
                                <a href="#"><i class="fa fa-share-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="review" class="pro-tab-content-inner col-12 p-0">
                        @if (!auth()->user())
                            <div class="btn-group"><span class="btn btn-danger btn-open-login"><i class="fa fa-lock"></i> |
                                    Login to comment.</span></div>
                        @endif
                        <div class="comment-area">
                            <h4><span class="comment-count-total">{{ $subcomments->count() }}</span> Comments</h4>
                            <section class="commented-content"
                            data-url="{{ route('asbab.news.getComment', ['id' => $blog->id]) }}"
                            id="loadmore-comment"></section>
                            <div class="d-flex justify-content-end mb-5">
                                <ul class="pagination" id="pagination">
                                </ul>
                            </div>
                        </div>
                        @if (auth()->user())
                            <form method="post" id="addition-comment-form" class="comm-addition"
                                data-action="{{ route('asbab.news.add_comment', ['id' => $blog->id]) }}"
                                enctype="multipart/form-data">
                                <input hidden type="text" name="parent_id" value="0" />
                                <textarea class="comment-summernote" name="details" cols="30" rows="10"></textarea>

                                <button class="fr-btn" type="submit"><i class="fab fa-telegram-plane"></i></button>
                            </form>
                        @endif
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
