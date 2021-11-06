(function ($) {
    "use strict";

    $(document).on('submit', '#form-profile', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, {
            success: function (response) {

                console.log(response);

            }
        });
    });

})(jQuery);