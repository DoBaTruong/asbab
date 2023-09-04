(function ($) {
    'use strict';
    /*-----------------------------------------------
    1.0 Active Particials Page Code
    -----------------------------------------------*/
    function accAccordion() {
        $('.account-area')
            .find('.acc-group').removeClass('active')
            .first().addClass('active');
        $('.account-area .acc-menu')
            .find('li').removeClass('active')
            .first().addClass('active');
        $('.account-area').on('click', '.acc-menu li', function () {
            $(this).addClass('active').parents('.account-navside').find('.acc-menu li').not(this).removeClass('active');
            let tarel = $($(this).children().first().data('target'));
            $(tarel).addClass('active').siblings('.acc-group').removeClass('active');
        });
        $('.account-area').on('click', '.order-btn-detail', function () {
            $(this).addClass('active').parents('.acc-order-group').find('.order-btn-detail').not(this).removeClass('active');
            $(this).parents('.order-group').find('.order-btn-close-detail, .order-btn-cancel').addClass('active')
        })

        $('.account-area').on('click', '.order-btn-close-detail', function () {
            $(this).parents('.order-group').find('.order-btn-close-detail, .order-btn-detail, .order-btn-cancel').removeClass('active')
        })
    };

    accAccordion();

    /*-----------------------------------------------
    2.0 Edital Profile Account Code
    -----------------------------------------------*/
    $(document).on('click', '.btn-edit-profile', function (e) {
        e.preventDefault();
        $(this).addClass('d-none')
            .parents('.row').find('#profile').addClass('active').children().find('input').attr('disabled', false);
        $('#profile').find('[name="pf_birdth"]').attr('type', 'date');
        $('#profile').find('.btn-alt-avata').removeClass('d-none');
        $('#profile').find('.gender-not-edit').addClass('d-none');
        $('#profile').find('.gender-edit').removeClass('d-none');
        $('#profile').find('[type="submit"]').removeClass('d-none');
    });

    if ($(window).width() < 767) {
        let elava = $('#profile .acc-table td#userava');
        elava.remove();
        $('#profile .acc-table tr:first-child').before('<tr />');
        $('#profile .acc-table tr:first-child').append(elava.attr({
            'colspan': '4',
            'rowspan': '1'
        }));
        $('#profile .acc-table').children().find('tr td:nth-child(3)').attr('colspan', '2');
    }

    $('#profile [name="pf_avata"]').on('change', function () {
        let parelvie = $(this).parents('.avar-container').find('#preview-ava-profile');
        parelvie.children().remove();
        let cf = $(this).get(0).files[0];
        if (cf) {
            let imgeli = parelvie.append($('<img />'));
            let reader = new FileReader();
            reader.onload = function (e) {
                imgeli.find('img').attr('src', e.target.result);
            }
            reader.readAsDataURL(cf);
        }
    })

    $('#profile').submit(function (e) {
        e.preventDefault();
        let that = $(this);
        let data = new FormData(that[0]);
        $.ajax({
            type: 'post',
            url: that.data('action'),
            dataType: 'json',
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                let gender;
                switch (data.gender) {
                    case '0':
                        gender = 'Male';
                        break;
                    case '1':
                        gender = 'Female';
                        break;
                    case '2':
                        gender = 'Others';
                        break;
                }
                $('#profile').parents('.row').find('.edit-badge').removeClass('d-none')
                that.removeClass('active').children().find('input').attr('disabled', true);
                that.find('.gender-not-edit').text(gender)
                if (data.birdth !== null) {
                    let date = data.birdth.split('-');
                    that.find('[name="pf_birdth"]').attr('type', 'text').val(date[2] + '/' + date[1] + '/' + date[0]);
                }
                that.find('.btn-alt-avata').addClass('d-none');
                that.find('.gender-not-edit').removeClass('d-none');
                that.find('.gender-edit').addClass('d-none');
                that.find('[type="submit"]').addClass('d-none');
                Swal.fire(
                    'Edited!',
                    'Your profile has been edited.',
                    'success'
                )
            }
        });
    });

    $('#resetpass').submit(function (e) {
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: 'post',
            url: that.data('action'),
            dataType: 'json',
            data: that.serialize(),
            success: function (data) {
                that.find('.error').remove();
                that.find('.alert-danger').removeClass('alert-danger');
                that.find('[name]').val('');
                Swal.fire(
                    'Reset!',
                    'Your password has been reset.',
                    'success'
                )
            },
            error: function (respon) {
                if (respon.status === 422) {
                    let errors = respon.responseJSON.errors;
                    that.find('[name]').each(function (ind, elem) {
                        if (errors[elem.name]) {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                        } else {
                            $(elem).removeClass('alert-danger').parents('.form-group').find('.error').remove();
                        }
                    })
                } else {
                    that.find('.error').remove();
                    that.find('.alert-danger').removeClass('alert-danger');
                }
            }
        });
    });

    /*-----------------------------------------------
    3.0 Cancel Order Code
    -----------------------------------------------*/
    $(document).on('click', '.order-btn-cancel', function (e) {
        e.preventDefault();
        let url = $(this).find('a').data('href');
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
                    url: url,
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
    })

})(jQuery)
