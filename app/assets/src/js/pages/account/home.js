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
            let fileName = $file.val().split('\\').pop();
            // console.log(fileName);
            let tmppath = fileName ? URL.createObjectURL(event.target.files[0]) : '';
            //Check successfully selection
            if (fileName) {
                $label.addClass('file-ok');
                $labelFrame.css('background-image', 'url(' + tmppath + ')');
                // $labelText.text(fileName);
                $labelText.text('Удалить фото');
            } else {
                $label.removeClass('file-ok');
                $labelText.text(labelDefault);
                $labelFrame.css('background-image', '');
            }
        });

        $file.on('click', function (event) {
            if ($label.hasClass('file-ok')) {
                event.preventDefault();
                $file.val('').change();
            }
        });
        // End loop of file input elements
    });

})(jQuery);
