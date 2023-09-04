(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#brands-table').length) {
        let urlAjax = $('#brands-table').data('url');
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
                data: 'image_path',
                name: 'image_path',
                class: 'text-center image'
            },
            {
                data: 'link',
                name: 'link'
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
                data: 'image_path',
                name: 'image_path',
                class: 'text-center image'
            },
            {
                data: 'link',
                name: 'link'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }
        $('#brands-table').DataTable({
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

    $('#editBrand').on('show.bs.modal', function (e) {
        let editUrl = $(e.relatedTarget).data('href');
        let that = $(this);
        that.find('.files-view').children().remove();
        $.ajax({
            type: 'GET',
            url: editUrl,
            dataType: 'json',
            success: function (data) {
                that.find('.modal-title').text('Edit: ' + data.brand.name);
                that.find('[name="url"]').val(data.url);
                that.find('[name="name"]').val(data.brand.name);
                that.find('[name="link"]').val(data.brand.link);
                that.find('.files-view').append('<span class="view-item"><img src="' + editUrl.split('admin')[0] + data.brand.image_path + '" /></span>')
            }
        });
    })

    $('#update-btn-brand').on('click', function (e) {
        e.preventDefault();
        let formData = new FormData($(this).parents('form')[0]);
        $.ajax({
            type: 'POST',
            url: $('#editBrand [name="url"]').val(),
            dataType: 'JSON',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#editBrand .error').remove();
                $('#editBrand .alert-danger').removeClass('alert-danger');
                $('#editBrand').modal('hide');
                Swal.fire(
                    'Edited!',
                    'Your brand has been edited.',
                    'success'
                )
                $('#brands-table').DataTable().ajax.reload();
            },
            error: function (data) {
                if (data.status === 422) {
                    let errors = data.responseJSON.errors;
                    $('#editBrand form [name]').each(function (ind, elem) {
                        if (errors[elem.name]) {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                        }
                    })
                } else {
                    $('#editBrand .error').remove();
                    $('#editBrand .alert-danger').removeClass('alert-danger');
                }
            }
        });
    })

    if ($('#brand-form').length) {
        $('#brand-form').on('submit', function (e) {
            e.preventDefault();
            let that = $(this);
            let formData = new FormData(this);
            $.ajax({
                type: 'post',
                url: that.data('action'),
                dataType: 'JSON',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    that.find('.error').remove();
                    that.find('.alert-danger').removeClass('alert-danger');
                    Swal.fire(
                        'Added!',
                        'Your brand has been added.',
                        'success'
                    )
                    that.find('[name]').val('').removeClass('alert-danger').parents('.form-group').find('.error').remove();
                    that.find('.file-choose-alt').removeClass('change btn-danger').addClass('choose btn-info').text('Choose');
                    that.find('.files-view').children().remove();
                    $('#brands-table').DataTable().ajax.reload();
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
                            'Your brand has been deleted.',
                            'success'
                        )
                        $('#brands-table').DataTable().ajax.reload();
                    }
                });
            }
        })
    }

})(jQuery)
