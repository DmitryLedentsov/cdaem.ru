(function ($) {
    "use strict";

    $(document).on('submit', '#form-profile', function (e) {
        e.preventDefault();
        window.ajaxRequest($(this));
    });

})(jQuery);