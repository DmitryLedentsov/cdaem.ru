(function ($) {
    "use strict";

    // tooltip info close contact
    $(document).on('click', '.adv-card .adv-card-safe-icon', function () {
        $(this).find('.adv-card-safe-tooltip').toggleClass('adv-card-safe-tooltip-active');
    });


    // change price value
    $(document).on('click', '.adv-card .adv-card-advs-price-value', function (e) {
        e.preventDefault();
        let $this = $(this);
        $this.closest('.adv-card-advs-price-item').siblings().find('.adv-card-advs-modal').removeClass('adv-card-advs-modal-active');
        $this.closest('.adv-card-advs-price-item').find('.adv-card-advs-modal').toggleClass('adv-card-advs-modal-active');
    });

    $(document).on('submit', '.adv-form', function (e) {
        e.preventDefault();
        window.ajaxRequest($(this), {
            success: function (response) {
                // console.log(111111, response);
            }
        });
    });

    // content active
    $(document).on('click', '.adv-card .adv-content-change, .adv-card .adv-content-inner', function () {
        let $this = $(this);
        $('.adv-card .adv-content-change').toggleClass('adv-content-change-active');
        $('.adv-card .adv-content-inner').toggleClass('adv-content-inner-active');
    });


    // open contact phone
    $(document).on('click', '.adv-card .adv-card-contact-number, .adv-card .adv-card-contact-change', function (e) {
        e.preventDefault();
        let $this = $(this);
        $('.adv-card .adv-card-contact-change').toggleClass('adv-card-contact-change-active');
        $this.closest('.adv-card-contact-phone').find('.adv-card-phones').toggleClass('adv-card-phones-active');
    });

})(jQuery);
