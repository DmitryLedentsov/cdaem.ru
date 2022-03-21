(function ($) {
    "use strict";

    // file input
    $('input[type="file"]').each(function () {
        // Refs
        var $file = $(this),
            $existImageId = $file.siblings('input[type="hidden"]'),
            $label = $file.closest('label'),
            $labelFrame = $label.find('.step-photo-frame'),
            $labelText = $label.find('span'),
            labelDefault = $labelText.text();

        // console.log($existImageId);

        const imageToggle = (on, path = '', resetExistId = true) => {
            if (on) {
                $label.addClass('file-ok');
                $labelFrame.css('background-image', 'url(' + path + ')');
                // $labelText.text(fileName);
                $labelText.text('Удалить фото');
            }
            else {
                $label.removeClass('file-ok');
                $labelText.text(labelDefault);
                $labelFrame.css('background-image', '');
            }

            resetExistId && $existImageId.length && $existImageId.val(''); // Обнуляем идентификатор существующей картинки, чтобы удалить его на сервере
        };

        const existFile = $file.data('preview-src');

        if (existFile) {
            imageToggle(true, existFile, false);
        }
        else {
            imageToggle(false, '', false);
        }

        // When a new file is selected
        $file.on('change', function (event) {
            let fileName = $file.val().split('\\').pop();
            let tmppath = fileName ? URL.createObjectURL(event.target.files[0]) : '';

            if (fileName) {
                imageToggle(true, tmppath);
            } else {
                imageToggle(false);
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
