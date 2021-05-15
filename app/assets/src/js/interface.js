(function ($) {
    "use strict";

    /*=========================================================================
        Mask for phone
    =========================================================================*/
    $('[type="tel"]').inputmask("+7(999)999-99-99");


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


})(jQuery);