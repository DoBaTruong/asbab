(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if ($('#products-table').length) {
        let urlAjax = $('#products-table').data('url');
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
                data: 'feature_image_path',
                name: 'feature_image_path',
                class: 'text-center image'
            },
            {
                data: 'price',
                name: 'price',
                class: 'text-center'
            },
            {
                data: 'status',
                name: 'status',
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
                data: 'feature_image_path',
                name: 'feature_image_path',
                class: 'text-center image'
            },
            {
                data: 'price',
                name: 'price',
                class: 'text-center'
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }

        $('#products-table').DataTable({
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
        $(document).on('click', '.action-delete', actionDelete);
    }

    function actionDelete(e) {
        e.preventDefault();
        let hrefData = $(this).data('href');
        Swal.fire({
            title: 'Bạn chắc chắn?',
            text: "Bạn sẽ không thể lấy lại sản phẩm này được!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Hủy',
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
                            'Sản phẩm đã được xóa.',
                            'success'
                        )
                        $('#products-table').DataTable().ajax.reload();
                    }
                });
            }
        })
    }

    if ($(".tags_select_choose").length) {
        $(".tags_select_choose").select2({
            tags: true,
            tokenSeparators: [',']
        });
    }

    if ($('#content-description').length) {
        $('#content-description').summernote({
            height: 200,
            minHeight: null,
            maxHeight: null,
            focus: true
        });
    }

    if ($('.file-choose-alt').length) {
        $('.file-choose-alt').click(function () {
            let filebtnel = $(this).parents('.file-btn').find('.file-choose');
            filebtnel.click();
            filebtnel.on('change', function () {
                let parelvie = $(this).parents('.form-group').find('.files-view');
                parelvie.children().remove();
                let cf = filebtnel.get(0).files;
                if (cf.length > 0 && parelvie) {
                    parelvie.parents('.form-group').find('.file-choose-alt').toggleClass('choose change btn-info btn-danger');
                    for (let i = 0; i < cf.length; i++) {
                        parelvie.append('<span class="view-item"><img /></span>');
                        let imgeli = parelvie.children().last().find('img');
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            imgeli.attr('src', e.target.result);
                        }
                        reader.readAsDataURL(cf[i]);
                    }
                }
            })
        })
    }
})(jQuery)
