(function ($) {
    'use strict';

    function format(n) {
        return parseFloat(n).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
    }

    if ($('#wishlist-table').length) {
        function createWLItem(prd, baseUrl, w) {
            let status = prd.quantity - prd.sell == 0 ? 'Out of stock' : 'In stock';
            let imagEl;
            if (w > 768 && w < 992) {
                imagEl = `<td class="prd-thumbnail">
                            <img src="${ baseUrl + '/' +  prd.feature_image_path }" alt="">
                            <div>${ prd.name }</div>
                        </td>`;
            } else {
                imagEl = `<td class="prd-thumbnail">
                                <img src="${ baseUrl + '/' + prd.feature_image_path }" alt="">
                            </td>
                            <td class="text-justify">${ prd.name }</td>`;
            }
            const template = `<tr class="text-center">
                                ${ imagEl }
                                <td>$${ format(prd.price) }</td>
                                <td>${ status }</td>
                                <td>
                                    <a data-url="${ baseUrl +'/cart/addcart/' + prd.id }" href="#" class="btn btn-small btn-success btn-add-cart"><i class="fa fa-cart-plus"></i></a>
                                    <a data-url="${ baseUrl +'/wishlist/remove/' + prd.id }" href="#" class="btn btn-small btn-danger btn-remove-wishlist"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>`;

            const templateXS = `<div class="single-product col-sm-6 col-12">
                                    <div class="product-item">
                                        <div class="prd-item-thumb">
                                            <a href="#"><img src="${ baseUrl + '/' + prd.feature_image_path }" alt=""></a>
                                        </div>
                                        <ul class="prd-item-action">
                                            <li><a data-url="${ baseUrl +'/cart/addcart/' + prd.id }" href="#" class="btn-add-cart"><i class="fas fa-shopping-bag"></i></a></li>
                                            <li><a data-url="${ baseUrl +'/wishlist/remove/' + prd.id }" href="#" class="btn-remove-wishlist"><i class="fa fa-times"></i></a></li>
                                        </ul>
                                        <div class="prd-item-infor">
                                            <div class="infor-content">
                                                <a href="${ baseUrl + '/product/' + prd.slug }">${ prd.name }</a>
                                                <div class="d-flex justify-content-between">
                                                    <p class="infor-price"><span class="old-price pr-3"></span>$${ format(prd.price) }</p>
                                                    <span class="text-muted mr-2">${ status }</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`

            if (w < 768) {
                let item = document.createElement('div');
                item.innerHTML = templateXS.trim();
                return item.firstChild;
            } else {
                let item = document.createElement('tbody');
                item.innerHTML = template.trim();
                return item.firstChild;
            }
        }

        function getData(page, items) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            return $.ajax({
                type: 'get',
                url: $('#wishlist-table').data('url'),
                data: {
                    'page': page,
                    'items': items
                },
                dataType: 'JSON'
            })
        }

        function display(pageIndex) {
            return getData(pageIndex, 9)
                .then((data) => {
                    if (data.products.length !== 0) {

                        let w = $('body').innerWidth();
                        let itemsPerPage = 9;
                        let offset = (pageIndex - 1) * itemsPerPage;
                        // walk over the movie items for the current page and add them to the fragment
                        let len = offset + itemsPerPage > data.products.length ? data.products.length : offset + itemsPerPage;
                        let baseUrl = data.baseUrl;
                        $('#wishlist-table').children().remove();
                        let showEl, bodyEl, thEl;
                        if (w < 768) {
                            showEl = $('#wishlist-table').append($(`<div class="row product-list-xs product-list">
                                                                </div>`));
                            bodyEl = showEl.find('.product-list-xs');
                        } else {
                            if (w > 768 && w < 991) {
                                thEl = '<th>Product</th>';
                            } else {
                                thEl = `<th>Image</th>
                                    <th class="text-justify">Name</th>`;
                            }
                            showEl = $('#wishlist-table').append($(`<table class="table table-bordered">
                                                                    <thead class="bg-info text-white">
                                                                        <tr class="text-center">
                                                                            ${ thEl }
                                                                            <th>Price</th>
                                                                            <th>Status</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>`));
                            bodyEl = showEl.find('tbody');
                        }
                        for (let i = offset; i < len; i++) {
                            let prd = data.products[i];
                            let item = createWLItem(prd, baseUrl, w);
                            bodyEl.append($(item))
                        }
                        $('#pagination').children().remove();
                        $('#pagination').append(data.pagination);
                    } else {
                        $('#wishlist-table').children().remove();
                        $('#wishlist-table').append('<div class="alert alert-danger">Not have product on wishlist.</div>');
                    }

                });
        }

        display(1)

        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            $(this).parents('#pagination').find('.active').removeClass('active');
            let page = parseInt($(this).data('page'));
            $(this).parents('#pagination').find('.page__item[data-page="' + page + '"]').parents('li').addClass('active');
            display(page)
        });

        $(document).on('click', '.btn-remove-wishlist', function (e) {
            e.preventDefault();
            let that = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "get",
                        url: that.data('url'),
                        dataType: "json",
                        success: function (response) {
                            let page = parseInt(that.parents('.wishlist-area').find('#pagination .active .page__item').data('page'));
                            let numChild = that.parents('#wishlist-table').find('tbody tr, .single-product');
                            Swal.fire(
                                'Remove!',
                                'Product has been remove from wishlist !',
                                'success'
                            ).then(() => {
                                if (page > 1) {
                                    if (numChild.length > 1) {
                                        display(page)
                                    } else {
                                        if (page - 1 >= 1) {
                                            display(page - 1)
                                        } else {
                                            $('#wishlist-table').children().remove();
                                            $('#wishlist-table').append('<div class="alert alert-danger">Not have product on wishlist.</div>');
                                        }
                                    }
                                } else {
                                    if (numChild.length <= 1) {
                                        $('#wishlist-table').children().remove();
                                        $('#wishlist-table').append('<div class="alert alert-danger">Not have product on wishlist.</div>');
                                    }
                                    that.parents('tr, .single-product').remove();
                                }
                            });
                        }
                    });
                }
            });
        });
    }

    if ($('#compare-table').length) {
        function createWLItem(prd, baseUrl, w) {
            let status = prd.status == 0 ? 'Out of stock' : 'In stock';
            const template = `<tr class="text-center compare-group-local">
                                <td class="prd-thumbnail">
                                    <img src="${ baseUrl + '/' + prd.image }" alt="">
                                    <div>${ prd.name }</div>
                                </td>
                                <td>$${ format(prd.price) }</td>
                                <td>
                                    <div class="infor-rating mb-1"
                                        data-stars="${ prd.stars / 0.05 }%">
                                        <span class="stars ml-0"><i class="far fa-star"></i><i class="far fa-star"></i><i
                                                class="far fa-star"></i><i class="far fa-star"></i><i
                                                class="far fa-star"></i></span>
                                    </div>
                                </td>
                                <td>${ status }</td>
                                <td>
                                    <a data-url="${ baseUrl +'/cart/addcart/' + prd.id }" href="#" class="btn btn-small btn-success btn-add-cart"><i class="fa fa-cart-plus"></i></a>
                                    <a data-id="${ prd.id }" href="#" class="btn btn-small btn-danger remove-compare-item"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>`;

            const templateXS = `<div class="single-product compare-group-local col-sm-6 col-12">
                                    <div class="product-item">
                                        <div class="prd-item-thumb">
                                            <a href="#"><img src="${ baseUrl + '/' + prd.image }" alt=""></a>
                                        </div>
                                        <ul class="prd-item-action">
                                            <li><a data-url="${ baseUrl +'/cart/addcart/' + prd.id }" href="#" class="btn-add-cart"><i class="fas fa-shopping-bag"></i></a></li>
                                            <li><a data-id="${ prd.id }" href="#" class="remove-compare-item"><i class="fa fa-times"></i></a></li>
                                        </ul>
                                        <div class="prd-item-infor">
                                            <div class="infor-content">
                                                <a href="${ baseUrl + '/product/' + prd.slug }">${ prd.name }</a>
                                                <div class="infor-rating mb-1 text-center" data-stars="${ prd.stars / 0.05 }%">
                                                    <span class="stars ml-0"><i class="far fa-star"></i><i class="far fa-star"></i><i
                                                    class="far fa-star"></i><i class="far fa-star"></i><i
                                                    class="far fa-star"></i></span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <p class="infor-price"><span class="old-price pr-3"></span>$${ format(prd.price) }</p>
                                                    <span class="text-muted mr-2">${ status }</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`

            if (w < 768) {
                let item = document.createElement('div');
                item.innerHTML = templateXS.trim();
                return item.firstChild;
            } else {
                let item = document.createElement('tbody');
                item.innerHTML = template.trim();
                return item.firstChild;
            }
        }

        function getCompare() {
            let compares = localStorage.getItem('asbab_compare') === null ? [] : JSON.parse(localStorage.getItem('asbab_compare'));
            let arrID = [];
            if (compares.length) {
                $.map(compares, function (item) {
                    arrID.push(item.id);
                });
            }

            return $.ajax({
                type: 'get',
                url: $('#compare-table').data('url'),
                data: {
                    'data': arrID,
                },
                dataType: 'JSON'
            })
        }

        function display() {
            return getCompare()
                .then((data) => {
                    console.log(data)
                    if (data.products.length !== 0) {
                        $('#compare-table').children().remove();
                        let showEl, bodyEl;
                        let w = $('body').innerWidth();
                        if (w < 768) {
                            showEl = $('#compare-table').append($(`<div class="row product-list-xs product-list">
                                                    </div>`));
                            bodyEl = showEl.find('.product-list-xs');
                        } else {
                            showEl = $('#compare-table').append($(`<table class="table table-bordered">
                                                        <thead class="bg-info text-white">
                                                            <tr class="text-center">
                                                                <th>Product</th>
                                                                <th>Price</th>
                                                                <th>Rating</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>`));
                            bodyEl = showEl.find('tbody');
                        }

                        for (let i = 0; i < data.products.length; i++) {
                            let prd = data.products[i];
                            let item = createWLItem(prd, data.baseUrl, w);
                            bodyEl.append($(item))
                        }

                        if ($('.infor-rating').length) {
                            $('.infor-rating').each(function (i, e) {
                                let star = $(e).data('stars') ? $(e).data('stars') : 0;
                                $(e).find('.stars').css('--width', star)
                            })
                        }
                    } else {
                        $('#compare-table').children().remove();
                        $('#compare-table').append('<div class="alert alert-danger">Not have product to compare.</div>');
                    }
                });
        }

        display()
    }

})(jQuery);
