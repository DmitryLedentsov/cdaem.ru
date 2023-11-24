/*=========================================================================
        Seo text
    =========================================================================*/
(function ($) {
    "use strict";

    $(document).on('click', '[data-seo-text]', function () {
        $(this).parent().toggleClass('txt-show');
    });
})(jQuery);