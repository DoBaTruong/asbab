(function($) {
    $(document).on('click', '#contacts li', function (e) {
        e.preventDefault();
        let that = $(this);
        that.parents('ul').find('li').removeClass('active');
        that.addClass('active');
        $.ajax({
            type: "get",
            url: document.documentURI + '/data/' + that.data('id'),
            dataType: "json",
            data: {
              'contact': that.data('contact')  
            },
            success: function (response) {
                that.find('.preview').removeClass('not-read');
                that.parents('#frame').find('form.message-input [name=user_id]').val(response.user.id);
                let contentUserEl = that.parents('#frame').find('.content .contact-profile');
                contentUserEl.children().remove();
                contentUserEl.attr('data-id', response.user.id);
                contentUserEl.append($(`<img src="${ document.documentURI.split('admin')[0] + response.user.avatar }" alt="" />
                                        <p>${ response.user.name }</p>`));
                let contentMessEl = that.parents('#frame').find('.content .messages ul');
                contentMessEl.children().remove();
                let messEl = '';
                let chats = response.chats;
                for(let i = 0; i < chats.length; i++) {
                    let classMess, imgPath;
                    if (chats[i].type == 'admin') {
                        classMess = 'sent';
                        imgPath = response.logo;
                    } else {
                        classMess = 'replies';
                        imgPath = chats[i].user.avatar != null ? chats[i].user.avatar : '';
                    }
                    messEl += `<li class="${ classMess }">
                                    <span>
                                        <img src="${ document.documentURI.split('admin')[0] + imgPath }" alt="" />
                                    </span>
                                    <p>${ chats[i].message }</p>
                                </li>`
                }
                contentMessEl.append(messEl)
            }
        });
    });

    $('form.message-input').submit(function (e) {  
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: "post",
            url: that.data('action'),
            data: that.serialize(),
            dataType: "json",
            success: function (response) {
                $('#frame .content .messages ul').append($(`<li class="sent">
                                                                <span>
                                                                    <img src="${ document.documentURI.split('admin')[0] + response.user.avatar }" alt="" />
                                                                </span>
                                                                <p>${ response.message.message }</p>
                                                            </li>`));
                that.find('input[name="message"]').val(null);
                $("#frame .content .messages").scrollTop($("#frame .content .messages")[0].scrollHeight)
            }
        });
    });

    $(document).on('input', '#search input', function (e) {
        let keyword = $(this).val();
        let idActive =$(this).parents('#frame').find('.content .contact-profile').data('id');
        getContactsMessage(keyword, idActive);
    });

    function getContactsMessage(keyword, idAct) {
        $.ajax({
            type: "get",
            url: document.documentURI.split('admin')[0] + 'admin/chat/search',
            data: {
                keyword: keyword
            },
            dataType: "json",
            success: function (data) {
                $('#contacts ul').children().remove();
                let resultEls = '';
                if (data.length) {
                    for (let i = 0; i < data.length; i++) {
                        const c = data[i];
                        resultEls += `<li data-contact="${ c.id }" data-id="${ c.repfor == 0 ? c.user_id : c.repfor }" class="contact ${ keyword.trim() == '' ? (c.user.id == idAct ? 'active' : '') : '' }">
                                            <div class="wrap">
                                                <img src="${ document.documentURI.split('admin')[0] + (c.user.avatar !== null ? c.user.avatar : 'images/avatar/default.jpg') }" alt="" />
                                                <div class="meta">
                                                    ${ c.user.online == 1 ? '<span class="contact-status online"></span>' : '<span class="contact-status"></span>' }
                                                    <p class="name">${ c.user.name }</p>
                                                    <p class="preview ${ c.read == 0 ? 'not-read' : '' }">${ c.message }</p>
                                                </div>
                                            </div>
                                        </li>`;
                    }
                } else {
                    resultEls += `<li style="padding: 10px 5px; color: #fff">No message to be find for keyword search.</li>`;
                }
                $('#contacts ul').append(resultEls);
            }
        });
    }
})(jQuery)