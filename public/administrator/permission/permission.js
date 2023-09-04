(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#permission-form [name=module_par]').on('blur', function (e) {
        let getUrl = $(this).data('url');
        let that = $(this);
        let actionGroupEl = that.parents('form').find('.module_actions');
        actionGroupEl.children().remove()
        $.ajax({
            type: 'GET',
            url: getUrl,
            dataType: 'json',
            data: {
                'module': that.val()
            },
            success: function (data) {
                let actionsEl = '';
                for (let i = 0; i < data.length; i++) {
                    actionsEl += '<label class="text-capitalize">' +
                                    '<input type="checkbox" name="name[]" value="' + data[i] + ' ' + that.val() + '" />  ' + data[i] +
                                '</label>'
                }
                actionGroupEl.append(actionsEl)
            }
        });
    })
})(jQuery)
