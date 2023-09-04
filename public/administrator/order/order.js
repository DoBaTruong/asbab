(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#orders-table').length) {
        let urlAjax = $('#orders-table').data('url');
        let columns;
        if ($('#orders-table').data('update') == 0) {
            columns = [{
                data: 'code',
                name: 'code'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center'
            },
            {
                data: 'amount',
                name: 'amount'
            }];
        } else {
            columns = [{
                data: 'check',
                name: 'check',
                class: 'text-center',
                orderable: false,
                searchable: false
            },
            {
                data: 'code',
                name: 'code'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center'
            },
            {
                data: 'amount',
                name: 'amount'
            }];
        }
        $('#orders-table').DataTable({
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
            columns: columns
        });
    }

    $(document).on('click', '[data-toggle="checkall"]', function (e) {
        $(this).parents('#orders-table').find($(this).data('target')).each(function (ind, el) {
            $(el).attr('checked') ? $(el).attr('checked', false) : $(el).attr('checked', true);
        });
    });

    $(document).on('click', '.btn-update-order-status', function (e) {
        e.preventDefault();
        let data = $(this).parents('form').serialize();
        let urlUpdate = $(this).data('href');
        if ($(this).is($('.btn-open-select-shipper'))) {
            let match = $.grep($(this).parents('form').serializeArray(), function (v) {
                if (v.name == 'order_id[]' && v.value !== '') {
                    return v;
                }
            });
            if (match.length) {
                $('#select_shipper').modal('show');

                $('#select_shipper form').submit(function (e) {
                    e.preventDefault();
                    let ship_id_el = $(this).find('[name="ship_id"]');
                    let note_el = $(this).find('[name="note"]');
                    if (ship_id_el.val() == '') {
                        ship_id_el.parents('.form-group').find('.error').remove()
                        ship_id_el.addClass('alert-danger').parents('.form-group').append('<div class="error">Please choose shipper!</div>');
                    } else {
                        ship_id_el.removeClass('alert-danger').parents('.form-group').find('.error').remove()
                    }

                    if (note_el.val() == '') {
                        note_el.parents('.form-group').find('.error').remove()
                        note_el.addClass('alert-danger').parents('.form-group').append('<div class="error">Please choose shipper!</div>');
                    } else {
                        note_el.removeClass('alert-danger').parents('.form-group').find('.error').remove()
                    }

                    if (note_el.val() !== '' && ship_id_el.val() !== '') {
                        let datanew = data + '&' + $(this).serialize();
                        sendStatusUpdate(datanew, urlUpdate);
                    }
                })
            }
        } else {
            sendStatusUpdate(data, urlUpdate);
        }

        function sendStatusUpdate(d, u) {
            $.ajax({
                type: 'post',
                url: u,
                dataType: 'json',
                data: d,
                success: function (data) {
                    Swal.fire(
                        'Updated',
                        'Your order has been updated',
                        'success'
                    ).then(() => {
                        $('#select_shipper').modal('hide');
                        $('#orders-table').DataTable().ajax.reload();
                    })
                }
            });
        }
    });
})(jQuery)
