(function ($) {
    "use strict";

    let paddingScroll = null;
    let $modal = $('.modal');

    $modal.on('hidden.bs.modal', function (e) {
        if ($('.modal.show').length) {
            $('body').addClass('modal-open').css('paddingRight', paddingScroll + 'px');
        }
    });
    $modal.on('show.bs.modal', function (e) {
        $('.modal.show').not(e.target).modal('hide');
    });
    $modal.on('shown.bs.modal', function (e) {
        let $body = $('body');
        if (paddingScroll === null) {
            paddingScroll = parseInt($body.css('paddingRight'));
        }
    });

})(jQuery);