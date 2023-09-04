(function ($) {
    if ($('.comment-summernote').length) {
        $('.comment-summernote').summernote({
            height: 150,
            forcus: true,
            onfocus: function (e) {
                $('#review').find('.commented-content').append(`<div class="entering-load"><span></span><span></span><span></span></div>`)
            },
            onblur: function (e) {
                $('#review').find('.commented-content .entering-load').remove();
            },
            disableResizeEditor: true,
            toolbar: [
                ["insert", ["link", "picture"]]
            ]
        });
    }

    $('#contact-form').submit(function (e) {
        e.preventDefault();
        let that = $(this);
            let data = new FormData(that[0]);
            $.ajax({
            type: 'post',
            url: that.data('action'),
            dataType: 'json',
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                that.find('.error').remove();
                that.find('.alert-danger').removeClass('alert-danger');
                that.find('[name]').val('');
                that.find('.note-editable').text('');

                Swal.fire(
                    'Added!',
                    'Your categry has been added.',
                    'success'
                )
            },
            error: function (respon) {
                if (respon.status === 422) {
                    let errors = respon.responseJSON.errors;
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
            },
        })
    })
})(jQuery)
