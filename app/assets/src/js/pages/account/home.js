(function ($) {
    "use strict";

    // tooltip info close contact
    $(document).on('click', '.adv-safe-icon', function () {
        $(this).find('.adv-safe-tooltip').toggleClass('adv-safe-tooltip-active');
    });

    // change price value
    $(document).on('click', '.adv-price-change', function (e) {
        e.preventDefault();
        let $this = $(this);
        $this.closest('.adv-price-item').siblings().find('.adv-modal').removeClass('adv-modal-active');
        $this.closest('.adv-price-item').find('.adv-modal').toggleClass('adv-modal-active');
    });

    // open contact phone
    $(document).on('click', '.adv-contact-change', function (e) {
        e.preventDefault();
        let $this = $(this);
        $this.toggleClass('adv-contact-change-active');
        $this.closest('.adv-contact-phone').find('.list-phone').toggleClass('list-phone-active');
    });

    // content active
    $(document).on('click', '.adv-content-change', function () {
        let $this = $(this);
        $this.toggleClass('adv-content-change-active');
        $this.next('.adv-content-inner').toggleClass('adv-content-inner-active');
    });


    // file input
    $('input[type="file"]').each(function () {
        // Refs
        var $file = $(this),
            $label = $file.closest('label'),
            $labelFrame = $label.find('.step-photo-frame'),
            $labelText = $label.find('span'),
            labelDefault = $labelText.text();

        // When a new file is selected
        $file.on('change', function (event) {
            var fileName = $file.val().split('\\').pop(),
                tmppath = URL.createObjectURL(event.target.files[0]);
            //Check successfully selection
            if (fileName) {
                $label
                    .addClass('file-ok');
                $labelFrame
                    .css('background-image', 'url(' + tmppath + ')');
                $labelText.text(fileName);
            } else {
                $label.removeClass('file-ok');
                $labelText.text(labelDefault);
            }
        });

        // End loop of file input elements
    });

})(jQuery);