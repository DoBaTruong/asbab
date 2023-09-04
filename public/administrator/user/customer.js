(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#customers-table').length) {
        let urlAjax = $('#customers-table').data('url');
        let columns;

        if (urlAjax.split('data/')[1] == 1) {
            columns = [{
                    data: 'check',
                    name: 'check',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
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
                    data: 'orders',
                    name: 'orders',
                    class: 'text-center'
                },
                {
                    data: 'amount',
                    name: 'amount',
                    class: 'text-center'
                },
                {
                    data: 'level',
                    name: 'level',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                }];
        } else {
            columns = [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'orders',
                    name: 'orders',
                    class: 'text-center'
                },
                {
                    data: 'amount',
                    name: 'amount',
                    class: 'text-center'
                },
                {
                    data: 'level',
                    name: 'level',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                }];
        }
        $('#customers-table').DataTable({
            processing: true,
            responsive: true,
            dom: '<"flex-between"lf>t<"flex-between"ip>',
            language: {
                processing: "<div id='loader'>Đang tải dữ liệu !</div>",
                paginate: {
                    previous: '← Trước',
                    next: 'Sau →'
                },
                search: 'Tìm',
                lengthMenu: '_MENU_ kết quả một trang',
                info: 'Hiển thị _START_ đến _END_ của _TOTAL_ kết quả'
            },
            serverSide: true,
            order: [0, 'desc'],
            ajax: urlAjax,
            columns: columns
        });
    }

    $(document).on('click', '[data-toggle="checkall"]', function (e) {
        $(this).parents('#customers-table').find($(this).data('target')).each(function (ind, el) {
            $(el).attr('checked') ? $(el).attr('checked', false) : $(el).attr('checked', true);
        });
    });

    $(document).on('click', '.btn-update-vip', function (e) {
        e.preventDefault();
        let data = $(this).parents('form').serialize();
        let urlUpdate = $(this).data('url');
        let match = $.grep($(this).parents('form').serializeArray(), function (v) {
            if (v.name == 'user_id[]' && v.value !== '') {
                return v;
            }
        });

        if (match.length) {
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
                        'Đã cập nhật',
                        'Thông tin khách hàng đã được cập nhật',
                        'success'
                    ).then(() => {
                        $('#customers-table').DataTable().ajax.reload();
                    })
                }
            });
        }
    });

})(jQuery)
