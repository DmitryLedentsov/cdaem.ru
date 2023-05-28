(function ($) {
    "use strict";

    /*=========================================================================
        Mask for phone
    =========================================================================*/
    // $('[type="tel"]').inputmask("+38(099)-999-99-99");
    $('[type="tel"]').inputmask("+7(999)-999-99-99");


    /*=========================================================================
        Scroll to top
    =========================================================================*/
    $(document).on('click', '[data-scroll-top]', function () {
        $("html, body").animate({ scrollTop: 0 }, 500);
        return false;
    });


    /*=========================================================================
        Close error by double click
    =========================================================================*/
    $(document).on('dblclick', '.invalid-feedback', function () {
        let $this = $(this);
        let $form = $this.parents('form');
        if ($form) {
            $this.parent('.form-group').find('.invalid-feedback').css('display', 'none');
        }
    });


    /*=========================================================================
        Header collapse
    =========================================================================*/
    $(document).on('click', '.header-collapse .user-change, .header-collapse .user-info-avatar, .header-collapse .user-info-data', function () {
        let $header = $('.header-collapse');
        $header.find('.user-info').prev('.header-message').find('.message-info').removeClass('message-info-active');
        $header.find('.user-change').toggleClass('user-change-active');
        $header.find('.user-info').find('.user-settings').toggleClass('user-settings-active');
    });

    $(document).on('click', '.header-message', function () {
        let $this = $(this);
        $this.next('.user-info').find('.user-change').removeClass('user-change-active');
        $this.next('.user-info').find('.user-settings').removeClass('user-settings-active');
        $this.find('.message-info').toggleClass('message-info-active');
    });
})(jQuery);
