(function ($) {

    /**
     * Отправить платежную форму
     */
    $(document).on('submit', '#payment-form', function (event) {
        event.preventDefault();
        var $this = $(this);
        window.ajaxRequest($this,{
            success: data => {
                if (data.hasOwnProperty('status') && data.status === 'success') {
                    $('#main-modal').modal('hide');
                }
            },
            commonError: message => {
                $('.payment-form > .section:last-child').after(
                    $('<div>', {class: 'alert alert-danger', role: 'alert'}).append(message)
                );
            }
        });
    });

})(jQuery);
