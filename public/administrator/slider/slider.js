(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#sliders-table').length) {
        let urlAjax = $('#sliders-table').data('url');
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
                data: 'description',
                name: 'description'
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
                data: 'description',
                name: 'description'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }
        $('#sliders-table').DataTable({
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

    $('#createSlider').on('show.bs.modal', function (e) {
        $(this).find('form [name]').each(function (i, el) {
            $(el).val('');
        });

        $(this).find('form .file-choose-alt').addClass('choose btn-info').removeClass('change btn-danger');
        $(this).find('.view-item').remove();
    })

    $(document).on('click','#sliders-table .action-edit', function (e) {
        let editUrl = $(this).data('href');
        let that = $('#editSlider');
        that.find('[name="description"]').text('');
        $.ajax({
            type: 'GET',
            url: editUrl,
            dataType: 'json',
            success: function (data) {
                that.find('.modal-title').text(data.slider.name);
                that.find('[name="name"]').val(data.slider.name);
                that.find('form .view-item img').attr('src', data.updateUrl.split('admin')[0] + data.slider.image_path);
                that.find('[name="description"]').text(data.slider.description);
                that.find('[type="submit"]').attr('data-url', data.updateUrl);
            }
        });
    });

    $('#add-btn-slider').on('click', function (e) {
        e.preventDefault();
        let storeUrl = $(this).data('url');
        let formData = new FormData($('#createSlider .slider-body')[0]);
        $.ajax({
            type: 'POST',
            url: storeUrl,
            dataType: 'JSON',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#createSlider').modal('hide');
                Swal.fire(
                    'Added!',
                    'Your slider has been added.',
                    'success'
                )
                $('#sliders-table').DataTable().ajax.reload();
            },
            error: function (data) {
                let errors = data.responseJSON.errors;
                $('#createSlider form [name]').each(function (ind, elem) {
                    if (errors[elem.name]) {
                        $(elem).parents('.form-group').find('.error').remove();
                        $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                    } else {
                        $(elem).removeClass('alert-danger').parents('.form-group').find('.error').remove();
                    }
                });
            }
        });
    })

    $('#edit-btn-slider').on('click', function (e) {
        e.preventDefault();
        let updateUrl = $(this).data('url');
        let formData = new FormData($('#editSlider .slider-body')[0]);
        $.ajax({
            type: 'POST',
            url: updateUrl,
            dataType: 'JSON',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#editSlider').modal('hide');
                Swal.fire(
                    'Edited!',
                    'Your slider has been edited.',
                    'success'
                )
                $('#sliders-table').DataTable().ajax.reload();
            },
            error: function (data) {
                if (data.status === 422) {
                    let errors = data.responseJSON.errors;
                    $('#editSlider form [name]').each(function (ind, elem) {
                        if (errors[elem.name]) {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                        } else {
                            $(elem).removeClass('alert-danger').parents('.form-group').find('.error').remove();
                        }
                    });
                }
            }
        });
    })

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
                            'Your slider has been deleted.',
                            'success'
                        )
                        $('#sliders-table').DataTable().ajax.reload();
                    }
                });
            }
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

})(jQuery)
