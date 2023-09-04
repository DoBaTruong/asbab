@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/assets/summernote/summernote.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/product/product.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/assets/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('guest/assets/infinite-ajax-scroll/infinite-ajax-scroll.min.js') }}"></script>
    <script src="{{ asset('guest/product/product.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="{{ route('asbab.home') }}">Home</a>
                        <span class="breadcrumb-item active">Products</span>
                    </nav>
                </div>
            </div>
        </section>
        <section class="prd-details">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-12">
                        <div class="prd-big-images">
                            @if (count($product->images) > 0)
                                <ul class="nav small-thumb-contain" id="thumbTab" role="tablist">
                                    @php
                                        $count = 0;
                                    @endphp
                                    @foreach ($product->images as $key => $image)
                                        @if ($count === 0)
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="{{ 'thumb-tab-' . $key }}"
                                                    data-toggle="tab" href="#{{ 'thumb-' . $key }}" role="tab"
                                                    aria-controls="{{ 'thumb-' . $key }}" aria-selected="true">
                                                    <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" />
                                                </a>
                                            </li>
                                        @else
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="{{ 'thumb-tab-' . $key }}" data-toggle="tab"
                                                    href="#{{ 'thumb-' . $key }}" role="tab"
                                                    aria-controls="{{ 'thumb-' . $key }}" aria-selected="false">
                                                    <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" />
                                                </a>
                                            </li>
                                        @endif
                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach
                                </ul>
                                <div class="tab-content big-thumb-contain" id="thumbContent">
                                    @php
                                        $count = 0;
                                    @endphp
                                    @foreach ($product->images as $key => $image)
                                        @if ($count === 0)
                                            <div class="tab-pane fade show active" id="{{ 'thumb-' . $key }}"
                                                role="tabpanel" aria-labelledby="{{ 'thumb-tab-' . $key }}">
                                                <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" />
                                            </div>
                                        @else
                                            <div class="tab-pane fade" id="{{ 'thumb-' . $key }}" role="tabpanel"
                                                aria-labelledby="{{ 'thumb-tab-' . $key }}">
                                                <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" />
                                            </div>
                                        @endif
                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach
                                </div>
                            @else
                                <div class="big-thumb-contain">
                                    <div class="tab-pane active">
                                        <img src="{{ asset($product->feature_image_path) }}" alt="{{ $product->name }}" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-7 col-12">
                        <div class="prd-item-infor">
                            <div class="infor-content">
                                <a>{{ $product->name }}</a>
                                <p class="mt-1 mb-1">Model: <span class="ml-2">MNG001</span></p>
                                <div class="infor-rating mb-1"
                                    data-stars="{{ $product->rates->avg('rate') / 0.05 . '%' }}">
                                    <span class="stars ml-0"><i class="far fa-star"></i><i class="far fa-star"></i><i
                                            class="far fa-star"></i><i class="far fa-star"></i><i
                                            class="far fa-star"></i></span>
                                </div>

                                <p class="infor-price"><span
                                        class="old-price pr-3">$30.3</span>${{ number_format($product->price, 2, '.', ',') }}
                                </p>
                            </div>
                            <p class="content-details">{!! trim(explode('II', explode('ABSTRACT:', strip_tags(html_entity_decode($product->details)))[1])[0]) !!}</p>
                            <p class="prd-status">Availability: <span
                                    class="text-muted">{{ $product->quantity > 0 ? 'In Stock' : 'Out Of Stock' }}</span>.
                            </p>
                            <div class="prd-tags">
                                <div>Product Tags:</div>
                                <div class="d-flex ml-2">
                                    @foreach ($product->tags as $tag)
                                        <a href="#">{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <form method="POST" class="prd-list-btn">
                                <div class="prd-quantity">
                                    <span class="qtt-btn qtt-minus"><i class="fa fa-minus"></i></span>
                                    <input type="number" name="prd_qtt" min="0" step="1" class="show-qtt"
                                        max="{{ $product->quantity }}" value="1" />
                                    <span class="qtt-btn qtt-plus"><i class="fa fa-plus"></i></span>
                                </div>
                                <div class="d-flex detail-action">
                                    <a href="#" class="fr-btn btn-add-cart"
                                        data-url="{{ route('asbab.cart.addcart', ['id' => $product->id]) }}">Add To
                                        Cart</button>
                                        <a class="fr-btn ml-2 btn-add-wishlist"
                                            data-info="{{ json_encode(['id' => $product->id]) }}" href="#"><i
                                                class="far fa-heart"></i></a>
                                        <a class="fr-btn ml-2 btn-add-compare"
                                            data-info="{{ json_encode(['id' => $product->id]) }}" href="#"><i
                                                class="fa fa-random"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="details-content-area w-100">
                        <ul class="nav details-content-tab" id="detailTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description"
                                    role="tab" aria-controls="description" aria-selected="true">DESCRIPTION</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab"
                                    aria-controls="review" aria-selected="false">REVIEW</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="shipping-tab" data-toggle="tab" href="#shippoli" role="tab"
                                    aria-controls="shippoli" aria-selected="false">DELIVERY & RETURNS</a>
                            </li>
                        </ul>
                        <div class="tab-content details-content-main" id="detailContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <div class="pro-tab-content-inner row flex-between">
                                    @php
                                        $detailsTable = explode('</tbody>', explode('<tbody>', explode('</table>', html_entity_decode($product->details))[0])[1])[0];
                                        $trDetails = [];
                                        foreach (explode('<tr', $detailsTable) as $key => $tr) {
                                            if ($key > 0) {
                                                $trDetails[] = '<tr' . $tr;
                                            }
                                        }
                                    @endphp

                                    @foreach ($trDetails as $key => $detail)
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <h4>{{ strip_tags(explode('</td>', $detail)[0] . '</td>') }}</h4>
                                            <p>{!! strip_tags(explode('</td>', $detail)[1] . '</td>', '<br>') !!}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                <div class="pro-tab-content-inner">
                                    @if (auth()->user())
                                        @if (empty($ratcheck))
                                            <form method="post"
                                                data-action="{{ route('asbab.product.stars', ['id' => $product->id]) }}"
                                                class="rating-form">
                                                <h5>Rating:</h5>
                                                <div class="d-flex justify-content-start">
                                                    <div class="prd-rating-rank">
                                                        <div class="star-group">
                                                            <input hidden class="rating-input" type="radio" name="rating"
                                                                id="rating-5" value="5" />
                                                            <label class="rating-label" aria-label="5 stars"
                                                                for="rating-5"><i
                                                                    class="rating-icon fa fa-star"></i></label>
                                                            <input hidden class="rating-input" type="radio" name="rating"
                                                                id="rating-4" value="4" />
                                                            <label class="rating-label" aria-label="4 stars"
                                                                for="rating-4"><i
                                                                    class="rating-icon fa fa-star"></i></label>
                                                            <input hidden class="rating-input" type="radio" name="rating"
                                                                id="rating-3" value="3" />
                                                            <label class="rating-label" aria-label="3 stars"
                                                                for="rating-3"><i
                                                                    class="rating-icon fa fa-star"></i></label>
                                                            <input hidden class="rating-input" type="radio" name="rating"
                                                                id="rating-2" value="2" />
                                                            <label class="rating-label" aria-label="2 stars"
                                                                for="rating-2"><i
                                                                    class="rating-icon fa fa-star"></i></label>
                                                            <input hidden class="rating-input" type="radio" name="rating"
                                                                id="rating-1" value="1" />
                                                            <label class="rating-label" aria-label="1 stars"
                                                                for="rating-1"><i
                                                                    class="rating-icon fa fa-star"></i></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-success">Send</button>
                                            </form>
                                        @endif
                                    @else
                                        <div class="btn-group"><span class="btn btn-danger btn-open-login"><i
                                                    class="fa fa-lock"></i> | Login to comment.</span></div>
                                    @endif
                                    <div class="comment-area">
                                        <section class="commented-content"
                                            data-url="{{ route('asbab.product.getComment', ['id' => $product->id]) }}"
                                            id="loadmore-comment">
                                        </section>
                                        <div class="d-flex justify-content-end mb-5">
                                            <ul class="pagination" id="pagination">
                                            </ul>
                                        </div>
                                    </div>
                                    @if (auth()->user())
                                        <form method="post" id="addition-comment-form" class="comm-addition"
                                            data-action="{{ route('asbab.product.add_comment', ['id' => $product->id]) }}"
                                            enctype="multipart/form-data">
                                            <input hidden type="text" name="parent_id" value="0" />
                                            <textarea class="comment-summernote" name="details" cols="30"
                                                rows="10"></textarea>

                                            <button class="fr-btn" type="submit"><i
                                                    class="fab fa-telegram-plane"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="shippoli" role="tabpanel" aria-labelledby="shipping-tab">
                                <div class="pro-tab-content-inner">
                                    @php
                                        $deliveryTable = explode('</tbody>', explode('<tbody>', explode('</table>', html_entity_decode($product->details))[1])[1])[0];
                                        $trDeliveries = [];
                                        foreach (explode('<tr', $deliveryTable) as $key => $tr) {
                                            if ($key > 0) {
                                                $trDeliveries[] = '<tr' . $tr;
                                            }
                                        }
                                    @endphp

                                    @foreach ($trDeliveries as $tr)
                                        <h4 class="text-capitalize">{{ strip_tags(explode('</td>', $tr)[0] . '</td>') }}
                                        </h4>
                                        <p>{{ strip_tags(explode('</td>', $tr)[1] . '</td>') }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <section class="container mb-100 prd-related">
            <h3 class="text-center">PRODUCT RELATED</h3>
            <div class="owl-related owl-carousel">
                @foreach ($relateds as $related)
                    <div class="product-item">
                        <div class="prd-item-thumb" style="background-color: #fff; border: 1px solid #ececec;">
                            <a><img style="width: 90%" src="{{ asset($related->feature_image_path) }}" alt="" /></a>
                        </div>
                        <ul class="prd-item-action">
                            <li><a class="btn-add-wishlist" href="#"
                                    data-info="{{ json_encode(['id' => $related->id]) }}"><i
                                        class="far fa-heart"></i></a></li>
                            <li><a href="#" class="btn-add-cart"
                                    data-url="{{ route('asbab.cart.addcart', ['id' => $related->id]) }}"><i
                                        class="fas fa-shopping-bag"></i></a></li>
                            <li><a class="btn-add-compare" href="#"
                                    data-info="{{ json_encode(['id' => $related->id]) }}"><i
                                        class="fa fa-random"></i></a></li>
                        </ul>
                        <div class="prd-item-infor">
                            <div class="infor-content">
                                <a class="product-name" href="{{ route('asbab.product.show', ['slug' => $related->slug]) }}">{{ $related->name }}</a>
                                <p class="infor-price"><span class="old-price pr-3"></span>${{ number_format($related->price, 2, '.', ',') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
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
