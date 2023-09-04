(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#delivery-table').length) {
        var urlAjax = $('#delivery-table').data('url');
        var oTable = $('#delivery-table').DataTable({
            processing: true,
            responsive: true,
            dom: '<"flex-between"lf>t<"flex-between"ip>',
            language: {
                processing: "<div id='loader'>Dang load nghe bay !</div>",
                paginate: {
                    previous: '← Prev',
                    next: 'Next →'
                },
                lengthMenu: '_MENU_ results per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ results'
            },
            serverSide: true,
            order: [0, 'desc'],
            ajax: urlAjax,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'province_id',
                name: 'province_id'
            },
            {
                data: 'district_id',
                name: 'district_id'
            },
            {
                data: 'ward_id',
                name: 'ward_id'
            },
            {
                data: 'feeship',
                name: 'feeship',
                class: 'text-center'
            }
            ]
        });

        $('#delivery-table').on('click', function (e) {
            let permission = $('#delivery-table').data('edit');
            if (permission == 1) {
                if ($(e.target).is($('input'))) {
                    let that = $(e.target);
                    that.attr('disabled', false)
                    that.on('keyup', function (e) {
                        if (e.which === 13) {
                            let editUrl = that.data('url');
                            $.ajax({
                                type: 'post',
                                url: editUrl,
                                dataType: 'JSON',
                                data: {
                                    'feeship': that.val()
                                },
                                success: function (data) {
                                    $('#delivery-table').DataTable().ajax.reload();
                                }
                            });
                        }
                    })
                }
            }
        })
    }

    if ($('#delivery-form').length) {
        $(window).on('load', function () {
            let selectel = $('#delivery-form [name="province_id"]');
            let getUrl = selectel.data('url');
            $.ajax({
                type: 'get',
                url: getUrl,
                dataType: 'JSON',
                success: function (data) {
                    let optionHtmls = '';
                    for (let i = 0; i < data.length; i++) {
                        optionHtmls += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    selectel.append(optionHtmls);
                }
            });
        })

        $('#delivery-form [name]').each(function (i, e) {
            let districtEl = $(this).parents('form').find('[name="district_id"]');
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
                                districtEl.attr('disabled', false)
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
                            }
                        });
                    } else {
                        wardEl.attr('disabled', true)
                    }
                })
            }
        });

        $('#delivery-form').on('submit', function (e) {
            e.preventDefault();
            let that = $(this);
            console.log('hahahah')
            $.ajax({
                type: 'post',
                url: that.data('action'),
                dataType: 'JSON',
                data: that.serialize(),
                success: function (data) {
                    that.find('.error').remove();
                    that.find('.alert-danger').removeClass('alert-danger');
                    that.find('[name]').val('').not($('[name="province_id"], [name="feeship"]')).attr('disabled', true)
                    Swal.fire(
                        'Added!',
                        'Your delivery has been added.',
                        'success'
                    )
                    $('#delivery-table').DataTable().ajax.reload();
                },
                error: function (data) {
                    if (data.status === 422) {
                        let errors = data.responseJSON.errors;
                        that.find('[name]').each(function (ind, elem) {
                            if (errors[elem.name]) {
                                $(elem).parents('.form-group').find('.error').remove();
                                $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                            }
                        });
                    } else {
                        that.find('.error').remove();
                        that.find('.alert-danger').removeClass('alert-danger');
                    }
                }
            });
        })
    }

})(jQuery)
