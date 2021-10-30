(function ($) {
  "use strict";

    $(document).on('submit', '#form-help', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, {
            success: function (response) {
                if (response.status === 'success') {
                    $form.hide();
                    $form.after(function() {
                        return '<div class="alert alert-success">' + response.message + '</div>';
                    });
                }
            }
        });
    });

})(jQuery);