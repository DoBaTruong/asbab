(function ($) {
    'use strict';

    /*-----------------------------------------------
    1.0 Infinite Scroll Blog Active
    -----------------------------------------------*/
    if ($('#infinite-news').length) {
        function createNewsItem(blog, baseUrl) {
            let date = (new Date(blog.updated_at)).toDateString();
            const template = `<div class="col-xl-4 col-md-6 single-blog">
                                    <div class="blog-item">
                                        <div class="blog-item-thumb">
                                            <a href="#"><img src="${ baseUrl + '/' + blog.image_path }" alt="" /></a>
                                        </div>
                                        <div class="blog-item-details">
                                            <div class="bl-date">
                                                <span>${ date.split(' ')[1] + ' ' + date.split(' ')[2] + ', ' + date.split(' ')[3] }</span>
                                            </div>
                                            <a href="${ baseUrl + '/news/' + blog.slug }">${ blog.title }</a>
                                            <p class="content-details limit-line">${ blog.abstract }</p>
                                            <div class="blog-btn">
                                                <a href="${ baseUrl + '/news/' + blog.slug }">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
            let item = document.createElement('div');
            item.innerHTML = template.trim();

            return item.firstChild;
        }

        function getData() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            return $.ajax({
                type: 'get',
                url: $('#infinite-news').data('url'),
                dataType: 'JSON'
            })
        }

        function nextHandler(pageIndex) {
            return getData()
                .then((data) => {
                    let frag = document.createDocumentFragment();
                    let itemsPerPage = 3;
                    let totalPages = Math.ceil(data.news.length / itemsPerPage);
                    let offset = pageIndex * itemsPerPage;

                    // walk over the movie items for the current page and add them to the fragment
                    let len = offset + itemsPerPage > data.news.length ? data.news.length : offset + itemsPerPage;
                    let baseUrl = data.baseUrl;

                    for (let i = offset; i < len; i++) {
                        let blog = data.news[i];

                        let item = createNewsItem(blog, baseUrl);

                        frag.appendChild(item);
                    }

                    let hasNextPage = pageIndex < totalPages - 1;
                    return this.append(Array.from(frag.childNodes))
                        .then(() => {
                            $('#infinite-news').find('.limit-line').each(function (i, e) {
                                let lineHeight = parseFloat($(e).css('lineHeight').split('px')[0]);
                                $(e).css('height', lineHeight * 4)
                            });
                        })
                        // indicate that there is a next page to load
                        .then(() => hasNextPage);
                });
        }

        function randomValues() {
            anime({
                targets: '.spinner .circle-el',
                translateX: function () {
                    return anime.random(0, 270);
                },
                easing: 'easeInOutQuad',
                duration: 750,
                complete: randomValues
            });
        }

        randomValues();

        function display() {
            window.ias = new InfiniteAjaxScroll('#infinite-news', {
                item: '.single-blog',
                next: nextHandler,
                pagination: false,
                spinner: '.spinner'
            });
        }

        display()
    }

    /*-----------------------------------------------
    2.0 Replace Summer Note For Comment Textarea Code
    -----------------------------------------------*/
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

    /*-----------------------------------------------
    7.0 Area Comment Code
    -----------------------------------------------*/
    $(document).on('click', '.btn-reply-comment', function (e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#addition-comment-form').offset().top - 120
        }, 1000)
        $('#addition-comment-form').find('[name="parent_id"]').val($(this).parents('.comment-item').data('id'))
    });

    if ($('.comm-addition').length) {
        $('.comm-addition').submit(function (e) {
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
                    that.find('.note-editable').text('');
                    that.find('[name="parent_id"]').val('0');
                    let countelcomm = that.parents('#review').find('h4 .comment-count-total');
                    countelcomm.text(parseInt(countelcomm.text()) + 1);
                    if (data.parent_id == 0) {
                        let eladd = that.parents('#review').find('.commented-content').prepend(data.htmlComment);
                        $('html, body').animate({
                            scrollTop: eladd.offset().top - 120
                        }, 1000);
                    } else {
                        let groupEl = that.parents('#review').find('.commented-content [data-id="' + data.parent_id + '"]').parents('.comment-group');
                        let commel = groupEl.find('.show-count-subcomment');
                        groupEl.find('.btn-load-or-hide').remove();
                        if (parseInt(commel.text()) > 1) {
                            groupEl.find('.comment-item:first-child').after($(`<div class="btn-load-or-hide btn-loadmore-subcomment" data-url="${ data.baseUrl + '/product/getSubComm/' + groupEl.find('.comment-item').data('id')  }" data-limit="4">
                                                                                    <span>Load more comment</span><span class="ml-1"><i class="fa fa-plus"></i></span>
                                                                                </div>`));
                        } else {
                            groupEl.find('.comment-item:first-child').after($(`<div class="btn-load-or-hide btn-hide-subcomment">
                                                                                    <span>Hide comment</span><span class="ml-1"><i class="fa fa-angle-up"></i></span>
                                                                                </div>`))
                        }
                        commel.text(parseInt(commel.text()) + 1);
                        let addEl = groupEl.append(data.htmlComment);
                        $('html, body').animate({
                            scrollTop: addEl.offset().top
                        }, 1000)
                    }
                }
            });
        });
    }

    $(document).on('click', '.btn-remove-comment', function (e) {
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: 'get',
            url: that.data('url'),
            dataType: 'json',
            success: function (data) {
                that.parents('#review').find('h4 .comment-count-total').text(data.count);
                if (data.parent_id == 0) {
                    that.parents('.comment-group').remove();
                } else {
                    that.parents('.comment-item').remove();
                }
            }
        });
    });

    $(document).on('click', '.btn-like-comment', function (e) {
        e.preventDefault();
        let that = $(this);
        let type = that.data('type');
        $.ajax({
            type: 'get',
            url: that.data('url'),
            dataType: 'json',
            data: {
                'type': type,
            },
            success: function (data) {
                that.toggleClass('text-danger');
                that.find('i').toggleClass('far fa');
                that.parents('a').find('.show-like-comment').text(data.likes);
            }
        })
    })

    $(document).on('click', '.btn-show-subcomment', function (e) {
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: 'get',
            url: that.data('url'),
            dataType: 'json',
            data: {
                'limit': 0
            },
            success: function (data) {
                let parentEl = that.parents('.comment-group');
                parentEl.children().not(':first-child').remove();
                let htmls = $(data.commhtmls);
                if (data.limit < data.count) {
                    parentEl.append($(`<div class="btn-load-or-hide btn-loadmore-subcomment" data-url="${ data.baseUrl + '/product/getSubComm/' + that.parents('.comment-item').data('id')  }" data-limit="4">
                                            <span>Load more comment</span><span class="ml-1"><i class="fa fa-plus"></i></span>
                                        </div>`))
                } else {
                    parentEl.append($(`<div class="btn-load-or-hide btn-hide-subcomment">
                                            <span>Hide comment</span><span class="ml-1"><i class="fa fa-angle-up"></i></span>
                                        </div>`))
                }
                for (let i = 0; i < data.limit; i++) {
                    parentEl.append(htmls[i]);
                }
            }
        })
    })

    $(document).on('click', '.btn-loadmore-subcomment', function (e) {
        e.preventDefault();
        let that = $(this);
        $.ajax({
            type: 'get',
            url: that.data('url'),
            dataType: 'json',
            data: {
                'limit': that.data('limit')
            },
            success: function (data) {
                let parentEl = that.parents('.comment-group');
                parentEl.children().not(':first-child').remove();
                let htmls = $(data.commhtmls);
                if (data.limit < data.count) {
                    parentEl.append($(`<div class="btn-load-or-hide btn-loadmore-subcomment" data-url="${ data.baseUrl + '/product/getSubComm/' + that.parents('.comment-item').data('id')  }" data-limit="4">
                                        <span>Load more comment</span><span class="ml-1"><i class="fa fa-plus"></i></span>
                                    </div>`))
                } else {
                    parentEl.append($(`<div class="btn-load-or-hide btn-hide-subcomment">
                                        <span>Hide comment</span><span class="ml-1"><i class="fa fa-angle-up"></i></span>
                                    </div>`))
                }

                let len = data.limit > data.count ? data.count : data.limit;
                for (let i = 0; i < len; i++) {
                    parentEl.append(htmls[i]);
                }
            }
        })
    })

    $(document).on('click', '.btn-hide-subcomment', function (e) {
        e.preventDefault();
        $(this).parents('.comment-group').children().not(':first-child').remove();
    })

    /*-----------------------------------------------
    7.0 Load More Sub Comments Product Code
    -----------------------------------------------*/
    if ($('#loadmore-comment').length) {
        function createProductItem(comment, baseUrl) {
            let class1 = comment.auth_like ? 'text-center' : '';
            let class2 = comment.auth_like ? 'fa' : 'far';
            const template = `<div class="comment-group">
                                    <div data-id="${ comment.id }" class="comment-item">
                                        <div class="comment-avatar">
                                            <img src="${ baseUrl + '/' + comment.users.avatar }" alt="User Avatar" />
                                        </div>
                                        <div class="comment-detail">
                                            <span class="comm-name">${ comment.users.name }</span>
                                            <span class="comm-time">${ comment.date_print }</span>
                                            <p class="comm-text">${ comment.comment }</p>
                                        </div>
                                        <div class="comm-action">
                                            <a class="btn-reply-comment" href="#"><i class="fa fa-pen-fancy"></i></a>
                                            <a class="btn-remove-comment" data-url="${ baseUrl + '/news/remove_comment/' + comment.id }" href="#"><i class="fa fa-trash-alt"></i></a>
                                            <a href="#" class="text-dark"><span class="btn-show-subcomment" data-url="${ baseUrl + '/news/getSubComm/' + comment.id }"><i class="far fa-comments"></i></span><span class="ml-1 show-count-subcomment">${ comment.count_comm }</span></a>
                                            <a href="#" class="text-dark"><span class="btn-like-comment ${ class1 }" data-url="${ baseUrl + '/news/like_comment/' + comment.id }"><i class="${ class2 } fa-thumbs-up"></i></span><span class="ml-1 show-like-comment">${ comment.likes.length }</span></a>
                                        </div>
                                    </div>
                                </div>`;
            let item = document.createElement('div');
            item.innerHTML = template.trim();

            return item.firstChild;
        }

        function getData(page) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            return $.ajax({
                type: 'get',
                url: $('#loadmore-comment').data('url'),
                data: {
                    'page': page
                },
                dataType: 'JSON',
            })
        }

        function display(pageIndex) {
            return getData(pageIndex)
                .then((data) => {
                    let itemsPerPage = 4;
                    let totalPages = Math.ceil(data.comments.length / itemsPerPage);
                    let offset = (pageIndex - 1) * itemsPerPage;
                    // walk over the movie items for the current page and add them to the fragment
                    let len = offset + itemsPerPage > data.comments.length ? data.comments.length : offset + itemsPerPage;
                    let baseUrl = data.baseUrl;
                    $('#loadmore-comment').children().remove();
                    for (let i = offset; i < len; i++) {
                        let comment = data.comments[i];
                        let item = createProductItem(comment, baseUrl);
                        $('#loadmore-comment').append(item);
                    }
                    $('#pagination').children().remove();
                    $('#pagination').append(data.pagination)
                });
        }

        display(1)

        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            $(this).parents('#pagination').find('.active').removeClass('active');
            let page = parseInt($(this).data('page'));
            $(this).parents('#pagination').find('.page__item[data-page="' + page + '"]').parents('li').addClass('active');
            display(page)
        });
    }


})(jQuery);
