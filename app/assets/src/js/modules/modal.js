(function ($) {
    "use strict";

    let paddingScroll = null;
    let $modal = $('.modal');

    $modal.on('hidden.bs.modal', function (e) {
        if ($('.modal.show').length) {
            $('body').addClass('modal-open').css('paddingRight', paddingScroll + 'px');
            let $form = $modal.find("form");
            if($form.length){
                $form.trigger('reset');
                $form.displayValidation('clear');
            }
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
        '    <div class="modal-dialog modal-dialog-centered">',
        '        <div class="modal-content">',
        '            <h3 class="modal-title text-left">&nbsp;</h3>',
        '            <button class="modal-close" data-dismiss="modal" aria-label="Close"></button>',
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

        $window.find('.modal-title').text(title);

        if (typeof description === 'string') {
            $window.find('.modal-body').html(description);
        } else if(typeof description === 'object') {
            $window.find('.modal-body').html('');
            $window.find('.modal-body').append(description);
        }

        if (type === 'large') {
            const $modalDialog = $window.find('.modal-dialog');
            $modalDialog.addClass('modal-lg');
        }
        $window.modal('show');
    };

    window.closeWindow = function () {
        let $window = $('#main-modal');
        $window.modal('hide');
    };
})(jQuery);
