(function ($) {
    "use strict";

    /*=========================================================================
        Seo text
    =========================================================================*/
    $(document).on('click', '[data-seo-text]', function () {
        $(this).parent().toggleClass('txt-show');
    });

})(jQuery);