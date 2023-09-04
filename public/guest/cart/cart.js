(function ($) {
    'use strict';

    function format(n) {
        return parseFloat(n).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
    }

    // Click Button Update Cart Shopping
    $(document).on('click', '.product-group-btn .btn-update-cart', function (e) {
        e.preventDefault();
        let listCart = $.grep($(this).parents('.container').find('.product-cart-list'), function (e) {
            if ($(e).css('display') !== 'none') {
                return $(e);
            }
        });
        let data = [];
        $(listCart).find('[name]').each(function (i, elem) {
            data[i] = {};
            data[i].id = $(elem).attr('name').split(']')[0].split('[')[1];
            data[i].quantity = $(elem).val();
        });
        let url = $(this).data('href');
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            data: {
                'data': data
            },
            success: function (respon) {
                if (respon.code === 200) {
                    $('#header-area .top-head-right .badge').text(respon.carts.length);
                    $('#header-area .shopping-cart .shopping-cart-wrap').children().remove();
                    let cartItems = '';
                    let totalsub = 0;
                    $.grep(respon.carts, function (c) {
                        totalsub += c.quantity * c.price;
                        cartItems += `<div class="shp-single-prd">
                                            <div class="shp-prd-infor d-flex">
                                                <div class="shp-prd-thumb">
                                                    <a href="#"><img src="${ respon.baseUrl + '/' + c.image_path }" alt="" /></a>
                                                </div>
                                                <div class="shp-prd-details">
                                                    <a href="#">${c.name}</a>
                                                    <span class="quantity">QTY: ${c.quantity}</span>
                                                    <span class="shp-price">$${format(c.price)}</span>
                                                </div>
                                            </div>
                                            <div class="prd-btn-remove">
                                                <a data-href="${respon.baseUrl + '/product/removecart/' + c.id}" class="btn-remove-cart"><i
                                                        class="fa fa-times"></i></a>
                                            </div>
                                        </div>`;
                    });
                    let feeship = respon.fee_ship ? respon.fee_ship.fee : 0;
                    $('#header-area .shp-cart-total .total-price').text('$' + format(totalsub));
                    $('#header-area .shopping-cart .shopping-cart-wrap').append(cartItems);
                    $('#checkout-area .shop-cart-total .cart-total-price').text('$' + format(totalsub));

                    $('#checkout-area .shop-cart-total .tax-fee-cart').text('$' + format(totalsub * 0.1));
                    $('#checkout-area .shop-cart-total .feeship-cart').text('$' + format(feeship));
                    $('#checkout-area .shop-cart-total .calc-total-cart').text('$' + format(totalsub * 1.1 + feeship));
                    if (respon.coupon) {
                        if (respon.coupon.type == 0) {
                            $('#checkout-area .shop-cart-total .coupon-cart').text('(- $' + format(respon.coupon.discount) + ')');
                        }
                        if (respon.coupon.type == 1) {
                            $('#checkout-area .shop-cart-total .coupon-cart').text('(- $' + format(totalsub * respon.coupon.discount / 100) + ')');
                        }
                    }

                    Swal.fire(
                        'Updated!',
                        'Your cart has been updated.',
                        'success'
                    )
                }
            }
        })
    });

    // Click Button Apply Coupon For Order
    $(document).on('click', '.shop-coupon-code .btn-coupon-check', function (e) {
        e.preventDefault();
        let code = $(this).parents('.shop-coupon-code').find('[name="coupon-code"]').val();
        let that = $(this);
        let url = $(this).data('href');
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            data: {
                'coupon_code': code
            },
            success: function (respon) {
                if (respon.code === 200) {
                    let couponEl = that.parents('.shop-coupon-code');
                    couponEl.find('[name="coupon-code"]').removeClass('alert-danger');
                    couponEl.find('.error').remove();
                    let totalFee = parseFloat($('.shop-cart-total').find('.cart-total-price').text().split('$')[1].split(',').join(''));

                    if (respon.coupon.type == 0) {
                        $('.shop-cart-total .coupon-cart').text('(- $' + respon.coupon.discount + ')');
                    }
                    if (respon.coupon.type == 1) {
                        $('.shop-cart-total .coupon-cart').text('(- $' + format(respon.coupon.discount * totalFee / 100) + ')');
                    }
                }
            },
            error: function (respon) {
                if (respon.status === 422) {
                    let couponEl = that.parents('.shop-coupon-code');
                    couponEl.find('[name="coupon-code"]').addClass('alert-danger');
                    couponEl.find('.error').remove();
                    couponEl.append('<div class="error">' + respon.responseJSON.message + '</div>');
                }
            }
        })
    });

    // Show Select Option Province - District - Ward
    $(window).on('load', function (e) {
        let urlEl = $('.shop-fee-ship [name="province_id"]');
        let url = urlEl.data('url');
        let provinceId = urlEl.data('province');
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'JSON',
            success: function (data) {
                let optionHtmls = '';
                for (let i = 0; i < data.length; i++) {
                    if (provinceId !== '' && provinceId === data[i].id) {
                        optionHtmls += '<option selected value="' + data[i].id + '">' + data[i].name + '</option>';
                    } else {
                        optionHtmls += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                }
                urlEl.children().not(':first-child').remove()
                urlEl.append(optionHtmls);
            }
        })
    });

    $('.shop-fee-ship [name]').each(function (i, e) {
        let districtEl = $(this).parents('.shop-fee-ship').find('[name="district_id"]');
        let that = $(e);
        if ($(e).is($('[name="province_id"]'))) {
            $(this).on('blur', function (e) {
                if ($(this).val() !== '') {
                    let getUrl = districtEl.data('url') + '/delivery/districts/' + $(this).val();
                    $.ajax({
                        type: 'get',
                        url: getUrl,
                        dataType: 'JSON',
                        success: function (data) {
                            let optionHtmls = '';
                            for (let i = 0; i < data.length; i++) {
                                optionHtmls += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                            }
                            districtEl.children().not(':first-child').remove()
                            districtEl.append(optionHtmls);
                            districtEl.attr('disabled', false);

                            writeAddress(that, '.checkout-area-left')
                        }
                    });
                } else {
                    districtEl.attr('disabled', true)
                }
            })
        }
        if ($(e).is($('[name="district_id"]'))) {
            $(this).on('blur', function (e) {
                let wardEl = $(this).parents('form').find('[name="ward_id"]');
                if ($(this).val() !== '') {
                    let getUrl = districtEl.data('url') + '/delivery/wards/' + $(this).val();
                    $.ajax({
                        type: 'get',
                        url: getUrl,
                        dataType: 'JSON',
                        success: function (data) {
                            let optionHtmls = '';
                            for (let i = 0; i < data.length; i++) {
                                optionHtmls += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                            }
                            wardEl.children().not(':first-child').remove()
                            wardEl.append(optionHtmls);
                            wardEl.attr('disabled', false)
                            writeAddress(that, '.checkout-area-left')
                        }
                    });
                } else {
                    wardEl.attr('disabled', true)
                }
            })
        }

        if ($(e).is($('[name="ward_id"]'))) {
            $(this).on('blur', function (e) {
                writeAddress(that, '.checkout-area-left')
            })
        }
    });

    // Click Button Calculator Fee Ship
    $(document).on('click', '.shop-fee-ship .btn-cal-fee', function (e) {
        e.preventDefault();
        let parentEl = $(this).parents('.shop-fee-ship');
        let provinceID = parentEl.find('[name="province_id"]').val();
        let districtID = parentEl.find('[name="district_id"]').val();
        let wardID = parentEl.find('[name="ward_id"]').val();
        let details_address = parentEl.find('[name="details_address"]').val();
        let basic_address = parentEl.find('[name="basic_address"]').val();

        $.ajax({
            type: 'get',
            url: parentEl.data('url') + '/checkout/calc_fee_ship',
            dataType: 'json',
            data: {
                'province_id': provinceID,
                'district_id': districtID,
                'ward_id': wardID,
                'details_address': details_address,
                'address': basic_address + ',' + details_address
            },
            success: function (data) {
                parentEl.find('.alert-danger').removeClass('alert-danger');
                parentEl.find('.error').remove();
                $('.shop-cart-total .feeship-cart').text('$' + data.feeship.fee);
                let totalEls = $('.shop-cart-total');
                let taxFee = parseFloat(totalEls.find('.tax-fee-cart').text().split('$')[1].split(',').join(''));
                let totalFee = parseFloat(totalEls.find('.cart-total-price').text().split('$')[1].split(',').join(''));
                $('.shop-cart-total .calc-total-cart').text('$' + format(taxFee + totalFee + data.feeship.fee))

                if (data.coupon) {
                    if (data.coupon.type === 0) {
                        $('.shop-cart-total .coupon-cart').text('(- $' + format(data.coupon.discount) + ')');
                    } else {
                        $('.shop-cart-total .coupon-cart').text('(- $' + format(data.coupon.discount * (taxFee + totalFee + data.feeship.fee) / 100) + ')');
                    }
                } else {
                    $('.shop-cart-total .coupon-cart').text('')
                }
            },
            error: function (respon) {
                if (respon.status === 422) {
                    let errors = respon.responseJSON.errors;
                    parentEl.find('[name]').each(function (ind, elem) {
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
    });

    function writeAddress(currentEl, parentSelector) {
        let provinceEl = currentEl.parents(parentSelector).find('[name="province_id"]');
        let districtEl = currentEl.parents(parentSelector).find('[name="district_id"]');
        let wardEl = currentEl.parents(parentSelector).find('[name="ward_id"]');
        if (provinceEl.val()) {
            var province = $($.grep(provinceEl.children(), function (e) {
                if ($(e).val() == provinceEl.val())
                    return $(e);
            })).text();
        }
        if (districtEl.val()) {
            var district = $($.grep(districtEl.children(), function (e) {
                if ($(e).val() == districtEl.val())
                    return $(e);
            })).text();
        }
        if (wardEl.val()) {
            var ward = $($.grep(wardEl.children(), function (e) {
                if ($(e).val() == wardEl.val())
                    return $(e);
            })).text();
        }

        if (province && district && ward) {
            currentEl.parents(parentSelector).find('[name="basic_address"]').val(ward + ', ' + district + ', ' + province)
        }
    }

    // Click Button Create Input Group Password Element
    $(document).on('click', '.shop-info-customer .create-account', function (e) {
        if ($(this).is($(':checked'))) {
            let passEl = `<div class="form-group customer_password">
                                <label class="text-white">Password:</label>
                                <input type="password" name="customer_password" class="form-control" />
                            </div>`;
            $(this).parents('.form-group').before(passEl);
        } else {
            $(this).parents('.shop-info-customer').find('.customer_password').remove()
        }
    });

    // Click Button Confirm Order
    $(document).on('click', '.shop-cart-total .payment-btn .fr-btn', function (e) {
        e.preventDefault();
        let that = $(this);
        let parentEl = that.parents('#checkout-area');
        let url = that.data('url');
        let total_price = parseFloat(parentEl.find('.cart-total-price').text().split('$')[1].split(',').join(''));
        let user_id = parentEl.find('[name="user_id"]').val();
        let customer_name = parentEl.find('[name="customer_name"]').val();
        let customer_mail = parentEl.find('[name="customer_mail"]').val();
        let customer_phone = parentEl.find('[name="customer_phone"]').val();
        let create_account = parentEl.find('[name="account"]:checked').val();
        let agreeTerm = parentEl.find('[name="agreeTerm"]:checked').val();
        let paymethod = parentEl.find('[name="paymethod"]:checked').val();
        let customer_password = create_account == 1 ? parentEl.find('[name="customer_password"]').val() : null;
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            data: {
                'user_id': user_id,
                'amount': total_price,
                'customer_name': customer_name,
                'customer_mail': customer_mail,
                'customer_phone': customer_phone,
                'customer_password': customer_password,
                'paymethod': paymethod,
                'create_account': create_account,
                'agreeTerm': agreeTerm
            },
            success: function (data) {
                parentEl.find('.error').remove();
                parentEl.find('.alert-danger').removeClass('alert-danger');
                location.replace(data)
            },
            error: function (respon) {
                if (respon.status === 422) {
                    let errors = respon.responseJSON.errors;
                    parentEl.find('[name]').each(function (ind, elem) {
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
    });
})(jQuery);
