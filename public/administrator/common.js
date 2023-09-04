(function ($) {
    /*------- Prevent Default Link Anchor Code --------*/
    if ($('a[href="#"]').length) {
        $('a[href="#"]').click(function (e) {
            e.preventDefault();
        });
    }

    /*------- Sidebar Show Code --------*/
    $('.sidebar-toggle-box').on("click", function () {
        $('#container').toggleClass('sidebar-closed');
        return false;
    })

    $('#sidebar').on('click', '.sidebar-menu li', function () {
        $(this).addClass('active').parents('.sidebar-menu').find('li').not(this).removeClass('active');
        $(this).find('.sidebar-icon-adjq').toggleClass('minus plus')
            .parents('.sidebar-menu').find('.sidebar-icon-adjq').not($(this).find('.sidebar-icon-adjq')).removeClass('minus').addClass('plus');
        $(this).find('.sidebar-sub').toggleClass('d-none')
            .parents('#sidebar').find('.sidebar-sub').not($(this).find('.sidebar-sub')).addClass('d-none');
    })

    /*------- Scroll Up Code --------*/
    $.scrollUp({
        scrollText: '<i class="fa fa-angle-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });

    /*------- Confirm Delete Code --------*/
    $('#confirmDelete').on('show.bs.modal', function (e) {
        let dataURL = $(e.relatedTarget).data('href');
        $(e.target).find('.btn.delete').attr('href', dataURL)
    });

    /*------- Click notification message --------*/
    $(document).on('click', '#header_inbox_bar .dropdown-toggle', function (e) {
        e.preventDefault();
        $.ajax({
            type: "get",
            url: document.documentURI.split('admin')[0] + 'admin/notification/message',
            dataType: "json",
            success: function (response) {
                let messNotiEls = '';
                if (response.length > 0) {
                    $('#header_inbox_bar .notify-inbox-count p').text(`You have ${ response.length } new messages.`);
                    for (let i = 0; i < response.length; i++) {
                        let message = response[i];
                        let timeNoti;
                        let time = parseInt(((new Date()).getTime() - (new Date(message.created_at)).getTime())/1000);
                        if (time < 60) {
                            timeNoti = 'Just Now';
                        } else {
                            if ((time/60) < 60) {
                                timeNoti = parseInt(time/60) + ' mins';
                            } else {
                                if (time/3600 < 24) {
                                    timeNoti = parseInt(time/3600) + ' hrs';
                                } else {
                                    timeNoti = parseInt(time/3600/24) + ' days';
                                }
                            }
                        }
                        messNotiEls += `<li>
                                            <a href="${ document.documentURI.split('admin')[0] + 'admin/chat' + '?active_id=' + message.id }">
                                                <span class="photo"><img alt="avatar"
                                                        src="${ document.documentURI.split('admin')[0] + (message.user.avatar !== null ? message.user.avatar : 'images/avatar/default.jpg') }"></span>
                                                <div class="inbox-contain">
                                                    <span class="subject">
                                                        <span class="from">${ message.user.name }</span>
                                                        <span class="time">${ timeNoti }</span>
                                                    </span>
                                                    <span class="message">
                                                        ${ message.message }
                                                    </span>
                                                </div>
                                            </a>
                                        </li>`;
                    }
                }
                $('#header_inbox_bar .notify-inbox-content').children().remove();
                $('#header_inbox_bar .notify-inbox-content').append($(messNotiEls));
            }
        });
    })

})(jQuery)