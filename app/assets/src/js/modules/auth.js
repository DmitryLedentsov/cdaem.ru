(function ($) {
    "use strict";

    // Login
    $(document).on('submit', '#form-signIn', function (e) {
        e.preventDefault();
        let $form = $(this);
        window.ajaxRequest($form  /*{
             beforeSend: function () {},
             complete: function () {},
             success: function () {}
         }*/);
    });

    // SignUp
    $(document).on('submit', '#form-signUp', function (e) {
        e.preventDefault();
        let $form = $(this);
        window.ajaxRequest($form  /*{
             beforeSend: function () {},
             complete: function () {},
             success: function () {}
         }*/);
    });

    // Password recovery
    $(document).on('submit', '#form-recovery', function (e) {
        e.preventDefault();
        let $form = $(this);
        window.ajaxRequest($form  /*{
             beforeSend: function () {},
             complete: function () {},
             success: function () {}
         }*/);
    });

    // Resend code activation
    $(document).on('submit', '#form-resend', function (e) {
        e.preventDefault();
        let $form = $(this);
        window.ajaxRequest($form  /*{
             beforeSend: function () {},
             complete: function () {},
             success: function () {}
         }*/);
    });

})(jQuery);