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

    // bootstrap modal template
    const defaultModal = $([
        '<div id="main-modal" class="modal fade">',
        '    <div class="modal-dialog">',
        '        <div class="modal-content">',
        '            <div class="modal-header">',
        '                <h5 class="modal-title">&nbsp;</h5>',
        '                <button type="button" data-dismiss="modal" class="close">',
        '                   <span>&times;</span>',
        '               </button>',
        '            </div>',
        '            <div class="modal-body">&nbsp;</div>',
        '        </div>',
        '    </div>',
        '</div>'
    ].join("\n"));

    // add bootstrap window to body
    $('body').append(defaultModal);

    //
    window.openWindow = function (title, description, type) {
        let $window = $('#main-modal');
        type = type || 'bg-info';

        // $window.find('.modal-header').addClass(type);
        $window.find('.modal-title').text(title);
        $window.find('.modal-body').text(description);

        $window.modal('show');
    };

})(jQuery);