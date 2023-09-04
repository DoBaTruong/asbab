(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#setting-table').length) {
        let urlAjax = $('#setting-table').data('url');
        $('#setting-table').DataTable({
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
                data: 'config_key',
                name: 'config_key'
            },
            {
                data: 'config_value',
                name: 'config_value'
            }
            ]
        });

        $('#setting-table').on('click', function (e) {
            let permission = $('#setting-table').data('edit');
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
                                    'config_value': that.val()
                                },
                                success: function (data) {
                                    $('#setting-table').DataTable().ajax.reload();
                                }
                            });
                        }
                    })
                }
            }
        })
    }

    if ($('#setting-form').length) {
        $('#setting-form').on('submit', function (e) {
            e.preventDefault();
            let that = $(this);
            $.ajax({
                type: 'post',
                url: that.data('action'),
                dataType: 'JSON',
                data: that.serialize(),
                success: function (data) {
                    that.find('.error').remove();
                    that.find('.alert-danger').removeClass('alert-danger');
                    that.find('[name]').val('')
                    Swal.fire(
                        'Added!',
                        'Your config has been added.',
                        'success'
                    )
                    $('#setting-table').DataTable().ajax.reload();
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
