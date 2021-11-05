(function ($) {
  "use strict";

    // Login
    $(document).on('submit', '#form-vacancy', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, {
            success: function (response) {
                if (response.status === 'success') {
                    $form.hide();
                    $form.after(function() {
                        return '<div class="alert alert-success mb-5">' + response.message + '</div>';
                    });
                }
            }
        });
    });

})(jQuery);