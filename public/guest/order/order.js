(function ($) {
    'use strict';

    function format(n) {
        return parseFloat(n).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
    }

    if ($('#orders-table').length) {
        function createOrdertem(order, baseUrl) {
            let statusEl;
            if (order.status == 0) {
                statusEl = '<i class="fa fa-question-circle mr-1"></i>Not Confirming';
            }

            if (order.status == 1) {
                statusEl = '<i class="fa fa-spinner mr-1"></i>Processing';
            }

            if (order.status == 2) {
                statusEl = '<i class="fa fa-shipping-fast mr-1"></i>Shipping';
            }

            if (order.status == 3) {
                statusEl = '<i class="fa fa-truck-loading mr-1"></i>Delivered';
            }

            if (order.status == 4) {
                statusEl = '<i class="fa fa-times-circle mr-1"></i>Canceled';
            }

            let date = new Date(order.updated_at);
            let day = date.getDate();
            let month = date.getMonth();

            function formatTime(t) {
                if (t < 10) {
                    t = "0" + t;
                }
                return t;
            }
            const template = `<tr class="order-abstract">
                                <td class="text-uppercase"><span class="weight-700">Code:</span> ${ order.code }</td>
                                <td><span class="weight-700">Date:</span> ${ formatTime(day) + '.' + formatTime(month) + '.' + date.getFullYear() }</td>
                                <td><span class="weight-700">Amount:</span> $${ format(order.amount) }</td>
                                <td class="text-right">${ statusEl }</td>
                            </tr>
                            <tr class="order-detail" data-id="${ order.id }">
                                <td colspan="4">
                                    <div class="d-flex btn-order justify-content-end"><a class="order-btn-detail" href="#">Details <i class="fa fa-caret-down"></i></a></div>
                                </td>
                            </tr>`;
            let item = document.createElement('tbody');
            item.innerHTML = template.trim();
            return item.children;
        }

        function getData(page, items, status) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            return $.ajax({
                type: 'get',
                url: $('#orders-table').data('url'),
                data: {
                    'page': page,
                    'items': items,
                    'status': status
                },
                dataType: 'JSON'
            })
        }

        function display(pageIndex, status) {
            return getData(pageIndex, 4, status)
                .then((data) => {
                    if (data.orders.length !== 0) {
                        let w = $('body').innerWidth();
                        let itemsPerPage = 4;
                        let offset = (pageIndex - 1) * itemsPerPage;
                        // walk over the movie items for the current page and add them to the fragment
                        let len = offset + itemsPerPage > data.orders.length ? data.orders.length : offset + itemsPerPage;
                        let baseUrl = data.baseUrl;
                        $('#orders-table').children().remove();
                        let WrapEl = $('#orders-table').append($(`<table class="table table-order-abstract">
                                                                        </thead>
                                                                        <tbody>
                                                                        </tbody>
                                                                    </table>`));
                        for (let i = offset; i < len; i++) {
                            let order = data.orders[i];
                            let item = createOrdertem(order, baseUrl, w);
                            WrapEl.find('tbody').append($(item))
                        }
                        $('#pagination').children().remove();
                        $('#pagination').append(data.pagination);
                    } else {
                        $('#orders-table').children().remove();
                        $('#orders-table').append('<div class="alert alert-danger">Not have product on wishlist.</div>');
                    }
                });
        }

        display(1, 0)

        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            $(this).parents('#pagination').find('.active').removeClass('active');
            let page = parseInt($(this).data('page'));
            $(this).parents('#pagination').find('.page__item[data-page="' + page + '"]').parents('li').addClass('active');
            display(page, 0)
        });

        $(document).on('click', '.order-btn-detail', function (e) {
            e.preventDefault();
            let that = $(this);
            that.parents('tr.order-detail').addClass('active')
                .parents('tbody').find('tr.order-detail').not(that.parents('tr.order-detail'))
                .removeClass('active').find('td').append($(`<div class="d-flex justify-content-end btn-order "><a class="order-btn-detail" href="#">Details <i class="fa fa-caret-down"></i></a></div>`))
                .children().not(':last-child').remove();
            that.parents('td').children().not(that.parents('div.d-flex')).remove();
            $.ajax({
                type: "get",
                url: document.documentURI + '/detail/' + that.parents('tr.order-detail').data('id'),
                dataType: "json",
                success: function (data) {
                    let containEl = $(`<table class="table table-order-detail">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>ID</th>
                                                    <th class="text-justify">Product</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>`);
                    let incre = 0;
                    that.parents('td').append(containEl)
                    let containBody = that.parents('td').find('tbody');
                    let dateOrder, statusOrder;
                    $.map(data.bills, function (item) {
                        incre++;
                        statusOrder = item.orders.status;
                        dateOrder = item.orders.updated_at;
                        let itemEl = $(`<tr class="text-center">
                                            <td>${ incre }</td>
                                            <td class="prd-ord-info text-justify">
                                                <div class="infor-thumb">
                                                    <img src="${  data.baseUrl + '/' + item.images.feature_image_path }" alt="" />
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="infor-text">${ item.product_name }</div>
                                                </div>
                                            </td>
                                            <td>$${ format(item.product_price) }</td>
                                            <td>${ item.quantity }</td>
                                            <td>$${ format(item.product_price * item.quantity) }</td>       
                                        </tr>`);
                        containBody.append(itemEl)
                    });
                    let timeout = ((new Date()).getTime() - (new Date(dateOrder)).getTime()) / (24 * 60 * 60 * 1000);
                    let cancelEl = timeout < 7 && statusOrder != 4 ? '<div class="btn-order text-left"><a class="order-btn-cancel" href="#"><i class="fa fa-times mr-2"></i> Cancel order</a></div>' : '';
                    let classEl = timeout < 7 && statusOrder != 4 ? 'justify-content-between' : 'justify-content-end';
                    that.parents('td').append($(`<div class="d-flex ${classEl} mb-5">
                                                    ${ cancelEl }
                                                    <div class="btn-order text-right"><a class="order-btn-close-detail" href="#">Hide Details<i
                                                                class="fa fa-caret-up ml-2"></i></a></div>
                                                </div>`))
                }
            });
        });

        $(document).on('click', '.order-btn-close-detail', function (e) {
            e.preventDefault();
            $(this).parents('.order-detail').removeClass('active').find('td').children().not(':first-child').remove();
        });

        $(document).on('click', '.order-btn-cancel', function (e) {
            e.preventDefault();
            let that = $(this);
            Swal.fire({
                title: '<strong>Reason</strong>',
                html: '<textarea id="reason" name="reason" rows="5" class="form-control"></textarea> ',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!',
                preConfirm: () => {
                    const reason = Swal.getPopup().querySelector('#reason').value;
                    if(!reason) {
                        Swal.showValidationMessage(`Please enter reason for order cancellation`);
                    }
                    return { reason: reason}
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'GET',
                        url: document.documentURI + '/cancel/' + that.parents('tr').data('id'),
                        dataType: 'json',
                        data: result.value,
                        success: function (data) {
                            Swal.fire(
                                'Canceled!',
                                'Your order has been canceled.',
                                'success'
                            )
                            .then(() => {
                                location.reload()
                            })
                        }
                    });
                }
            })
        });

        $(document).on('click', '#select-show-order', function (e) {
            e.preventDefault();
            console.log('hahhaah')
            let that = $(this);
            display(1, that.val());
        });
    }

})(jQuery);
