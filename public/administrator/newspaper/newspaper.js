(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if ($('#news-table').length) {
        let urlAjax = $('#news-table').data('url');
        let columns;
        if (urlAjax.split('data/')[1] == 0) {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'image_path',
                name: 'image_path',
                class: 'text-center image'
            },
            {
                data: 'abstract',
                name: 'abstract',
                class: 'content'
            },
            {
                data: 'authors',
                name: 'authors',
                class: 'text-center'
            }];
        } else {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'image_path',
                name: 'image_path',
                class: 'text-center image'
            },
            {
                data: 'abstract',
                name: 'abstract',
                class: 'content'
            },
            {
                data: 'authors',
                name: 'authors',
                class: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }
        $('#news-table').DataTable({
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
                            'Your news has been deleted.',
                            'success'
                        )
                        $('#news-table').DataTable().ajax.reload();
                    }
                });
            }
        })
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
