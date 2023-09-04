(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if ($('#roles-table').length) {
        let urlAjax = $('#roles-table').data('url');
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
                data: 'description',
                name: 'description',
                class: 'text-justify'
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
                data: 'description',
                name: 'description',
                class: 'text-justify'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }
        $('#roles-table').DataTable({
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
                lengthMenu: '_MENU_ kết quả mỗi trang',
                info: 'Hiển thị _START_ đến _END_ của _TOTAL_ kết quả'
            },
            serverSide: true,
            order: [0, 'desc'],
            ajax: urlAjax,
            columns: columns
        });
        $(document).on('click', '.action-delete', actionDelete);
    }

    if($('.check-all').length) {
        $('.check-all').on('click', function () {
            $(this).parents().find('.check-child, .check-parent').prop('checked', $(this).prop('checked'))
        });
    }

    if($('.check-parent').length) {
        $('.check-parent').on('click', function () {
            $(this).parents('.permission').find('.check-child, .check-parent').prop('checked', $(this).prop('checked'))
        });
    }

    function actionDelete(e) {
        e.preventDefault();
        let hrefData = $(this).data('href');
        Swal.fire({
            title: 'Bạn chắc chắn?',
            text: "Bạn sẽ không thể lấy lại nội dung này!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Chắc chắn, xóa!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'GET',
                    url: hrefData,
                    dataType: 'json',
                    success: function (data) {
                        Swal.fire(
                            'Đã xóa!',
                            'Vai trò này đã được xóa.',
                            'success'
                        )
                        $('#roles-table').DataTable().ajax.reload();
                    }
                });
            }
        })
    }
})(jQuery)
