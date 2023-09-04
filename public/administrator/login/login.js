(function ($) {
    'use strict';
    $(".form-login form").validate({
        errorElement: "div",
        rules: {
            password: {
                required: true,
                minlength: 6
            },
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            password: {
                required: "Vui lòng nhập mật khẩu !",
                minlength: "Mật khẩu phải có độ dài tối thiểu 6 ký tự !"
            },
            email: "Email không đúng định dạng."
        }
    });

})(jQuery)
