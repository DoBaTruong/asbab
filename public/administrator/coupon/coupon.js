(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#coupons-table').length) {
        let urlAjax = $('#coupons-table').data('url');
        let columns;
        if (urlAjax.split('data/')[1] == 0) {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'code',
                name: 'code',
                class: 'text-center'
            },
            {
                data: 'discount',
                name: 'discount',
                class: 'text-center'
            },
            {
                data: 'quantity',
                name: 'quantity',
                class: 'text-center'
            }];
        } else {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'code',
                name: 'code',
                class: 'text-center'
            },
            {
                data: 'discount',
                name: 'discount',
                class: 'text-center'
            },
            {
                data: 'quantity',
                name: 'quantity',
                class: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }
        $('#coupons-table').DataTable({
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
        $(document).on('click', '.action-delete', actionDelete);
    }

    $(document).on('click', '.btn-send-coupon a', function (e) {
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: 'GET',
            url: that.data('href'),
            dataType: 'json',
            success: function (data) {
                Swal.fire(
                    'Send!',
                    'Your coupon has been send.',
                    'success'
                )
            }
        });
    });

    function actionDelete(e) {
        e.preventDefault();
        let hrefData = $(this).data('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'GET',
                    url: hrefData,
                    dataType: 'json',
                    success: function (data) {
                        Swal.fire(
                            'Deleted!',
                            'Your coupon has been deleted.',
                            'success'
                        )
                        $('#coupons-table').DataTable().ajax.reload();
                    }
                });
            }
        })
    }

})(jQuery)
