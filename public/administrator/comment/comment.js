(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if ($('#product-comments-table').length) {
        var urlAjax = $('#product-comments-table').data('url');
        $('#product-comments-table').DataTable({
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
                    data: 'stt',
                    name: 'stt'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'comments',
                    name: 'comments',
                    class: 'text-center'
                },
                {
                    data: 'rating',
                    name: 'rating',
                    class: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    if ($('#news-comments-table').length) {
        var urlAjax = $('#news-comments-table').data('url');
        $('#news-comments-table').DataTable({
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
                    data: 'stt',
                    name: 'stt'
                },
                {
                    data: 'post',
                    name: 'post'
                },
                {
                    data: 'comments',
                    name: 'comments',
                    class: 'text-center'
                },
                {
                    data: 'view',
                    name: 'view',
                    class: 'text-center'
                },
                {
                    data: 'like',
                    name: 'like',
                    class: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    $(document).on('click', '.btn-comment-reply', function (e) {
        e.preventDefault();
        let that = $(this);
        let commID = that.parents('tr').data('id');
        let slug = that.parents('tbody').data('item');

        Swal.fire({
            html: '<textarea id="reply" name="reply" rows="5" class="form-control"></textarea> ',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Send',
            preConfirm: () => {
                const reply = Swal.getPopup().querySelector('#reply').value;
                if(!reply) {
                    Swal.showValidationMessage(`Please enter reply content.`);
                }
                return { reply: reply}
            }
        }).then((result) => {
            if (result.value) {
                result.value.parent_id = commID;
                $.ajax({
                    type: 'GET',
                    url: document.documentURI.split('admin')[0] + 'admin/comment/' + that.parents('tbody').data('type') + '/' + slug + '/reply',
                    dataType: 'json',
                    data: result.value,
                    success: function (data) {
                        Swal.fire(
                            'Replied!',
                            'Your comment has been replied.',
                            'success'
                        )
                        .then(() => {
                            location.reload()
                        })
                    }
                });
            }
        })
    });

    $(document).on('click', '.btn-comment-delete', actionDelete);

    function actionDelete(e) {
        e.preventDefault();
        let hrefData = $(this).parents('tbody').data('href');
        let id = $(this).parents('tr').data('id');
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
                    data: {
                        'id': parseInt(id)
                    },
                    dataType: 'json',
                    success: function (data) {
                        Swal.fire(
                            'Deleted!',
                            'Comments has been deleted.',
                            'success'
                        )
                        .then(() => {
                            location.reload();
                        })
                    }
                });
            }
        })
    }
})(jQuery)
