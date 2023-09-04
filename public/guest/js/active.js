(function ($) {
    'use strict';
    $(document).on('click', '[href="#"]', function (e) {
        e.preventDefault();
    })

    function format(n) {
        return parseFloat(n).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if (document.documentURI.match('#login_account')) {
        $('#login_account').toggleClass('is-visiable');
    }

    /*-----------------------------------------------
    1.0 Search Bar
    -----------------------------------------------*/
    $('.search-btn-open').on("click", function () {
        $('body').toggleClass('search-box-show');
        return false;
    })

    $('.search-btn-close').on("click", function () {
        $('body').toggleClass('search-box-show');
        return false;
    })

    /*-----------------------------------------------
    2.0 Cart Box
    -----------------------------------------------*/
    $('.cart-btn-open').on("click", function () {
        $('.shopping-cart').addClass('shopping-cart-on');
        $('.body-overlay').addClass('is-visible');
    })

    $('.cart-btn-close').on("click", function () {
        $('.shopping-cart').removeClass('shopping-cart-on');
        $('.body-overlay').removeClass('is-visible');
    })

    $('.body-overlay').on("click", function () {
        $(this).removeClass('is-visible');
        $('.shopping-cart').removeClass('shopping-cart-on');
    })

    /*-----------------------------------------------
    3.0 Scroll Up && Sroll Header
    -----------------------------------------------*/
    $.scrollUp({
        scrollText: '<i class="fa fa-angle-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });

    var win = $(window);
    var sticky_id = $("#header-area");
    win.on('scroll', function () {
        var scroll = win.scrollTop();
        if (scroll < 245) {
            sticky_id.removeClass("scroll-header");
        } else {
            sticky_id.addClass("scroll-header");
        }
    });

    /*-----------------------------------------------
    4.0 Mean Menu
    -----------------------------------------------*/
    $('.mobile-icon').on("click", function () {
        $('.mobile-icon i').toggleClass('fa-bars fa-times');
        $('#mobile-menu .nav').toggleClass('d-block');
    })

    /*-----------------------------------------------
    5.0 Limit Line Code
    -----------------------------------------------*/
    if ($('.limit-line').length) {
        $('.limit-line').each(function (i, e) {
            let lineHeight = parseFloat($(e).css('lineHeight').split('px')[0]);
            $(e).css('height', lineHeight * 4)
        })
    }

    /*-----------------------------------------------
    6.0 Login Account Code
    -----------------------------------------------*/
    $(document).on("click", '.btn-open-login', function () {
        $('#login_account').toggleClass('is-visiable');
        if ($(this).data('toggle') == 'chat') {
            $('#login_account').attr('data-from', 'chat');
        }
        return false;
    })

    $('#login_account .btn-close-login').on("click", function () {
        $('#login_account').toggleClass('is-visiable');
        return false;
    })

    $('#login_account form').submit(function (e) {
        e.preventDefault();
        let that = $(this);
        let urlPost = that.data('action');
        let datafrom = that.parents('#login_account').data('from');
        $.ajax({
            type: 'post',
            url: urlPost,
            dataType: 'json',
            data: that.serialize(),
            success: function (data) {
                that.find('.alert-danger').removeClass('alert-danger');
                that.find('.error').remove();
                if (datafrom == 'chat') {
                    sessionStorage.setItem('chat_with_shop', 'true');
                }
                location.replace(document.documentURI.split('#login_account')[0]);
            },
            error: function (respon) {
                if (respon.status === 422) {
                    let errors = respon.responseJSON.errors;
                    that.find('[name]').each(function (ind, elem) {
                        if (errors[elem.name]) {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                        } else {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).removeClass('alert-danger')
                        }
                    });
                }
            }
        })
    })

    /*-----------------------------------------------
    7.0 Product Add Related, Compare Function Code
    -----------------------------------------------*/
    function addLocalStorage(btnSelector, dataName, parentSelect, imgSelector, localName, countLimit, viewFunc) {
        $(document).on('click', btnSelector, function (e) {
            let that = $(this);
            let inforProduct = that.data(dataName);
            if (that.is($('.product-name'))) {
                inforProduct.name = that.text();
            } else {
                inforProduct.name = that.parents(parentSelect).find('.product-name').text();
            }
            inforProduct.image_path = that.parents(parentSelect).find(imgSelector).attr('src');
            inforProduct.url = that.attr('href');

            let old_related = localStorage.getItem(localName) === null ? [] : JSON.parse(localStorage.getItem(localName));

            let idMatches = $.grep(old_related, function (obj) {
                return obj.id === inforProduct.id;
            });

            if (old_related.length <= countLimit) {
                if (!idMatches.length) {
                    old_related.push(inforProduct);
                    localStorage.setItem(localName, JSON.stringify(old_related));
                }
            }
            viewFunc()
        })
    }

    addLocalStorage('.prd-item-action .btn-add-compare', 'info', '.product-item', '.prd-item-thumb img', 'asbab_compare', 4, viewCompare)


    viewCompare()

    function viewCompare() {
        if ($('.compare-list').length) {
            let compares = $(JSON.parse(localStorage.getItem('asbab_compare')));
            let itemprels = '';
            let lastel = `<li><a href="#" class="remove-compare-all">Clear all</a><a href="${$('body').data('url_base') + '/compare'}">Compare</a></li>`;
            if (compares.length == 0) {
                $('.compare-list').children().remove();
                $('.compare-list').prepend('<li>No have product to compare !</li>');
            } else {
                compares.each(function (i, com) {
                    itemprels += `<li class="compare-group-local"><a href="#">${com.name}</a><a data-id="${com.id}" href="#" class="remove-compare-item"><i class="fa fa-trash"></i></a></li>`;
                });
                $('.compare-list').children().remove();
                $('.compare-list').prepend(itemprels).append(lastel);
            }
        }
    }

    $(document).on('click', '.remove-compare-item', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        let old_compare = localStorage.getItem('asbab_compare') === null ? [] : JSON.parse(localStorage.getItem('asbab_compare'));
        let new_compare = $.grep(old_compare, function (el) {
            if (el.id !== id) {
                return el;
            }
        })

        localStorage.setItem('asbab_compare', JSON.stringify(new_compare));

        $(this).parents('.compare-group-local').remove()
    });

    $(document).on('click', '.widget-compare .remove-compare-all', function (e) {
        e.preventDefault();
        $(this).parents('ul.compare-list').children().remove();
        $('.widget-compare ul.compare-list').append('<li>No have product to compare !</li>');
        localStorage.removeItem('asbab_compare');
    });

    $(document).on('click', '.btn-add-wishlist', function (e) {
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: "get",
            url: $('body').data('url_base') + '/wishlist/add',
            data: that.data('info'),
            dataType: "json",
            success: function (data) {
                Swal.fire(
                    'Added',
                    'Product has been added on Wishlist.',
                    'success'
                )
            }
        });
    });

    /*-----------------------------------------------
    8.0 Add/Remove Cart Ajax Function Code
    -----------------------------------------------*/
    $(document).on('click', '.btn-add-cart', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let quantity;
        let quantityEl = $(this).parents('form').find('[name="prd_qtt"]');
        if (quantityEl.length) {
            quantity = parseInt(quantityEl.val());
        } else {
            quantity = 1;
        }
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            data: {
                'quantity': quantity
            },
            success: function (data) {
                if (data.code === 200) {
                    let item = 0;
                    data.carts.forEach(p => {
                        item += parseInt(p.quantity);
                    });
                    $('#header-area .top-head-right .badge').text(item);
                    $('#header-area .shopping-cart .shopping-cart-wrap').children().remove();
                    let cartItems = '';
                    let cartTotalPrice = 0;
                    $.grep(data.carts, function (c) {
                        cartItems += `<div class="shp-single-prd cart-product-item">
                                            <div class="shp-prd-infor d-flex">
                                                <div class="shp-prd-thumb">
                                                    <a href="#"><img src="${ data.baseUrl + '/' + c.image_path}" alt="" /></a>
                                                </div>
                                                <div class="shp-prd-details">
                                                    <a href="#">${c.name}</a>
                                                    <span class="quantity">QTY: ${c.quantity}</span>
                                                    <span class="shp-price">$${format(c.price)}</span>
                                                </div>
                                            </div>
                                            <div class="prd-btn-remove">
                                                <a data-href="${data.baseUrl + '/cart/removecart/' + c.id}" class="btn-remove-cart"><i
                                                        class="fa fa-times"></i></a>
                                            </div>
                                        </div>`;
                        cartTotalPrice += c.quantity * c.price;
                    })
                    $('#header-area .shopping-cart .shopping-cart-wrap').append(cartItems);
                    $('#header-area .shopping-cart .total-price').text('$' + format(cartTotalPrice))
                    Swal.fire(
                        'Added!',
                        'Your product has been added.',
                        'success'
                    )
                }
            }
        });
    });

    $(document).on('click', '.btn-remove-cart', function (e) {
        e.preventDefault();
        let url = $(this).data('href');
        let that = $(this);
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (data) {
                if (data.code === 200) {
                    $('#header-area .top-head-right .badge').text(data.carts.length);
                    that.parents('.cart-product-item').remove();
                    if (data.carts.length === 0) {
                        $('#header-area .shopping-cart-wrap').append('<div class="shp-single-prd cart-product-item text-center">No have product on cart !</div>')
                    }
                    Swal.fire(
                        'Removed!',
                        'Your product has been removed.',
                        'success'
                    ).then(() => {
                        location.reload()
                    })
                }
            }
        });
    });

    /*-----------------------------------------------
    8.0 Chat with shop js code
    -----------------------------------------------*/
    function setChat() {
        let be = $('#chat-with-shop');
        $(document).on('click', '.chat-title-icon', function (e) {
            e.preventDefault();
            be.toggleClass('active');
            let that = $(this);
            if (that.parents('#chat-with-shop.active').length) {
                getContentChat(be)
                let wrap = be.find('.chat-meassage');
                if (wrap.find('.message-item').length == 0) {
                    wrap.append($(`<li class="message-item mess-welcome replies">
                                        <span class="mess-img">
                                            <img src="${be.data('url').split('chat')[0] + '/guest/images/logo/logo.png'}" alt="" />
                                        </span>
                                        <p>Greetings to you ! Do you need any help ?</p>
                                    </li>`))
                }
            } else {
                sessionStorage.removeItem('chat_with_shop');
            }
        })
    }

    function getContentChat(be) {
        $.ajax({
            type: "get",
            url: be.data('url'),
            dataType: "json",
            success: function (response) {
                let wrapMessEL = be.find('.chat-wrap .chat-meassage');
                wrapMessEL.children().not('.mess-welcome').remove();
                if (response.login == 1) {
                    let chats = response.chats;
                    let messageTemplates = '';
                    for (let i = 0; i < chats.length; i++) {
                        let classType, photo;
                        if (chats[i].type == 'client') {
                            classType = 'sent';
                            photo = chats[i].user.avatar !== null ? chats[i].user.avatar : response.default;
                        } else {
                            classType = 'replies';
                            photo = response.logo;
                        }
                        messageTemplates += `<li class="message-item ${classType}">
                                                    <span class="mess-img">
                                                        <img src="${ be.data('url').split('chat')[0] + photo}" alt="" />
                                                    </span>
                                                    <p>${chats[i].message}</p>
                                                </li>`;
                    }
                    wrapMessEL.append(messageTemplates);
                    $("#chat-with-shop .chat-meassage").scrollTop($("#chat-with-shop .chat-meassage")[0].scrollHeight)
                } else {
                    wrapMessEL.append($(`<li data-toggle="chat" class="btn-select-chat btn-open-login"><span class="mr-2"><i class="fa fa-lock"></i></span>Login to chat</li>`));
                    be.find('#chat-inbox').addClass('hidden');
                }
            }
        });
    }

    setChat()

    $('#chat-inbox').submit(function (e) {
        e.preventDefault();
        let that = $(this);
        let formComment = new FormData(that[0]);
        $.ajax({
            contentType: false,
            processData: false,
            url: that.data('action'),
            type: 'POST',
            dataType: 'json',
            data: formComment,
            success: function (response) {
                $(`<li class="sent"><span class="mess-img"><img src="${ response.baseUrl + '/' + response.user.avatar}" alt="" /></span><p>${response.message.message}</p></li>`).appendTo($('#chat-with-shop .chat-meassage'));
                $('#chat-inbox input').val(null);
            }, error: function () {
                alert("Have the error!");
            }
        });
    });

    if (sessionStorage.getItem('chat_with_shop') == 'true') {
        let be = $('#chat-with-shop');
        be.toggleClass('active');
        getContentChat(be);
        let wrap = be.find('.chat-meassage');
        if (wrap.find('.message-item').length == 0) {
            wrap.append($(`<li class="message-item mess-welcome replies">
                                        <span class="mess-img">
                                            <img src="${be.data('url').split('chat')[0] + '/guest/images/logo/logo.png'}" alt="" />
                                        </span>
                                        <p>Greetings to you ! Do you need any help ?</p>
                                    </li>`));
        }
    }

    $(document).on('click', '.btn-select-chat', function (e) {
        e.preventDefault();
        $('#chat-with-shop .chat-meassage').children().not('.mess-welcome').remove();
        $('#chat-inbox').removeClass('hidden');
    });

    /*-----------------------------------------------
    9.0 code search by js
    -----------------------------------------------*/
    $('#search-enter').submit(function (e) {
        e.preventDefault();
        let formData = new FormData($(this)[0]);
        display(1, formData, 'post');
    });

    function createSearchItem(data) {
        let nameResult, abstract;
        if (data.searchtype == 0) {
            nameResult = data.name;
            abstract = $(data.details).text();
        } else {
            nameResult = data.title;
            abstract = data.abstract;
        }

        let time = new Date(data.created_at);
        let timeResult = time.getDate() + ' ' + time.toDateString().split(' ')[1] + ', ' + time.getFullYear();

        const template = `<div class="result-item">
                            <a href="${data.url}" class="result-name">${nameResult}</a>
                            <p class="result-time">${timeResult}</p>
                            <p class="result-abstract">${abstract}</p>
                        </div>`;
        let item = document.createElement('div');
        item.innerHTML = template.trim();

        return item.firstChild;
    }

    function getData(page, formData, type) {
        formData.page = page;
        return $.ajax({
            type: type,
            url: $('#search-enter').data('action') + '/search',
            data: formData,
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
        })
    }

    function display(pageIndex, formData, type) {
        return getData(pageIndex, formData, type)
            .then((data) => {
                let results = Object.values(data.data);
                let wrapResultsElem = $('#search-results');
                wrapResultsElem.children().remove();
                $('#search-pagination .pagination').children().remove();
                if (results.length) {
                    let itemsPerPage = 9;
                    let totalPages = Math.ceil(results.length / itemsPerPage);
                    let offset = (pageIndex - 1) * itemsPerPage;
                    // walk over the movie items for the current page and add them to the fragment
                    let len = offset + itemsPerPage > results.length ? results.length : offset + itemsPerPage;
                    for (let i = offset; i < len; i++) {
                        let result = results[i];
                        let item = createSearchItem(result);
                        wrapResultsElem.append($(item));
                    }

                    let lineHeight = parseFloat(wrapResultsElem.find('.result-abstract').css('lineHeight').split('px')[0]);
                    wrapResultsElem.find('.result-abstract').css('height', lineHeight * 5);

                    let paginations = '';
                    if (totalPages > 1) {
                        if (pageIndex > 1) {
                            let prev = pageIndex - 1;
                            paginations += `<li><a href="${ data.searchUrl }" data-page="${prev}" data-keyword="${ data.keyword }" class="page-link page__prev">Prev</a></li>`;
                        }
                        for (let i = 1; i <= totalPages; i++) {
                            let active = pageIndex == i ? 'active' : '';
                            paginations += `<li class="${active}"><a href="${ data.searchUrl }" data-keyword="${ data.keyword }" data-page="${i}" class="page-link page__item">${i}</a></li>`;
                        }
                        if (pageIndex < totalPages) {
                            let next = pageIndex + 1;
                            paginations += `<li><a href="${ data.searchUrl }" data-page="${next}" data-keyword="${ data.keyword }" class="page-link page__next">Next</a></li>`;
                        }
                    }
                    $('#search-pagination .pagination').append($(paginations))
                } else {
                    wrapResultsElem.append($(`<span class="text-white">No have results to be find for "${ data.keyword }"</span>`))
                }
            });
    }

    $(document).on('click', '#search-pagination .page-link', function(e) {
        e.preventDefault();
        let pageIndex = $(this).data('page');
        let url = $(this).attr('href');
        let dataForm = {
            'keyword': $(this).data('keyword')
        }
        display(pageIndex, dataForm, 'get');
    });
})(jQuery);
