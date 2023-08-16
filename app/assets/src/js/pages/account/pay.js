$(function () {

    /**
     * Отправить платежную форму
     */
    $(document).on('submit', '.payment-form', function (event) {
        event.preventDefault();
        var $this = $(this);

        // нужно использовать window.ajaxRequest из js/ajax.js

        console.log($this.attr('action'));
        console.log($this);
    });

});