(function ($) {
    "use strict";

    $('input[name="User[phone]"]').removeAttr('disabled');
    $('input[name="User[email]"]').removeAttr('disabled');

    $(document).on('submit', '#form-profile', function (e) {
        e.preventDefault();
        window.ajaxRequest($(this));
    });

})(jQuery);
