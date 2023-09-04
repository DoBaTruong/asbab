(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if ($('#supports-table').length) {
        let urlAjax = $('#supports-table').data('url');
        let columns;
        if (urlAjax.split('data/')[1] == 0) {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'user',
                name: 'user'
            },
            {
                data: 'subject',
                name: 'subject',
                class: 'text-center'
            },
            {
                data: 'require',
                name: 'require'
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
                data: 'user',
                name: 'user'
            },
            {
                data: 'subject',
                name: 'subject',
                class: 'text-center'
            },
            {
                data: 'require',
                name: 'require'
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center'
            },
            {
                data: 'access',
                name: 'access',
                orderable: false,
                searchable: false
            }];
        }
        $('#supports-table').DataTable({
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
        $(document).on('click', '.btn-support-delete', actionDelete);
    }

    $(document).on('click', '.btn-support-reply', function (e) {
        e.preventDefault();
        let that = $(this);

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
                $.ajax({
                    type: 'GET',
                    url: that.data('href'),
                    dataType: 'json',
                    data: result.value,
                    success: function (data) {
                        Swal.fire(
                            'Approved!',
                            'Your require has been approved.',
                            'success'
                        )
                        .then(() => {
                            $('#supports-table').DataTable().ajax.reload();
                        })
                    }
                });
            }
        })
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
                            'Your support has been deleted.',
                            'success'
                        )
                        $('#supports-table').DataTable().ajax.reload();
                    }
                });
            }
        })
    }
})(jQuery)
