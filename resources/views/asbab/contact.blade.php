@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/assets/summernote/summernote.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/contact/contact.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/assets/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/contact/contact.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="{{ route('asbab.home') }}">Home</a>
                        <span class="breadcrumb-item active">Contact Us</span>
                    </nav>
                </div>
            </div>
        </section>

        <section class="contact-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-6 col-12">
                        <div id="googleMap" class="h-100">
                            <iframe class="w-100 h-100"
                                src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d3725.1501422969095!2d105.85136861488266!3d20.986617786021224!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x3135ac1521e6aaab%3A0x809e14beba0f439!2zVklOQVdJTkQsIDE2NCBOZ3V54buFbiDEkOG7qWMgQ-G6o25oLCBUxrDGoW5nIE1haSwgSG_DoG5nIE1haSwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!3m2!1d20.9865507!2d105.85396039999999!5e0!3m2!1svi!2s!4v1613376380130!5m2!1svi!2s"
                                width="600" height="450" frameborder="0" style="border:0;" allowfullscreen=""
                                aria-hidden="false" tabindex="0">
                            </iframe>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6 col-12">
                        <h3>Contact Us</h3>
                        <div class="contact-item">
                            <span class="item-icon"><i class="fa fa-map-marker-alt"></i></span>
                            <div class="item-details">
                                <div class="detail-contain">
                                    <h4 class="ct-title">our address</h4>
                                    <span>{{ $settings->where('config_key', 'shop_address')->first()->config_value }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="item-icon"><i class="fa fa-envelope"></i></span>
                            <div class="item-details">
                                <div class="detail-contain">
                                    <h4 class="ct-title">Email Address</h4>
                                    <span>{{ $settings->where('config_key', 'shop_email')->first()->config_value }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="item-icon"><i class="fa fa-phone"></i></span>
                            <div class="item-details">
                                <div class="detail-contain">
                                    <h4 class="ct-title">Phone number</h4>
                                    <span>{{ $settings->where('config_key', 'shop_phone')->first()->config_value }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="contact-form-contain col-12">
                        <h3>SEND A MAIL</h3>
                        <form id="contact-form" data-action="{{ route('asbab.contact.question') }}">
                            <div class="d-flex form-contain">
                                <div class="form-group w-50 pr-3">
                                    <input type="text" name="name" class="form-control" placeholder="Your name*" />
                                    <div class="form-message"></div>
                                </div>
                                <div class="form-group w-50 pl-3">
                                    <input type="text" name="email" class="form-control" placeholder="Mail*" />
                                    <div class="form-message"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="subject" type="text" name="subject" placeholder="Subject" />
                                <div class="form-message"></div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control comment-summernote" name="details" name="message" cols="30" rows="10"
                                    placeholder="Your message"></textarea>
                                <div class="form-message"></div>
                            </div>
                            <button type="submit" class="fr-btn">Send Message</button>
                        </form>
                    </div>
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
