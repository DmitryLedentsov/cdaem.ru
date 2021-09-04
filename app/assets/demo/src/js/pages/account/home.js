(function ($) {
    "use strict";

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