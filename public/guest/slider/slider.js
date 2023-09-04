(function ($) {
    'use strict';
    
    /*-----------------------------------------------
    1.0 Slider Activations
    -----------------------------------------------*/
    if ($('.owl-wrap').length) {
        $('.owl-wrap').owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            smartSpeed: 1000,
            autoplay: true,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplayTimeout: 10000,
            items: 1,
            dots: false,
            lazyLoad: true,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 1,
                },
                767: {
                    items: 1,
                },
                991: {
                    items: 1,
                }
            }
        });
    }

    $('.owl-brand').owlCarousel({
        loop: true,
        margin: 0,
        nav: false,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        smartSpeed: 1000,
        autoplay: true,
        autoplayTimeout: 10000,
        items: 5,
        dots: false,
        autoplayHoverPause: true,
        lazyLoad: true,
        responsive: {
            0: {
                items: 2,
            },
            767: {
                items: 4,
            },
            991: {
                items: 5,
            }
        }
    })

    $('.owl-related').owlCarousel({
        loop: true,
        margin: 20,
        nav: false,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        smartSpeed: 1000,
        autoplay: true,
        autoplayTimeout: 10000,
        items: 4,
        dots: false,
        autoplayHoverPause: true,
        lazyLoad: true,
        responsive: {
            0: {
                items: 1,
            },
            575: {
                items: 2,
            },
            767: {
                items: 3,
            },
            991: {
                items: 4,
            }
        }
    })

})(jQuery);