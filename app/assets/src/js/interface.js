(function ($) {
    "use strict";

    /*=========================================================================
        Mask for phone
    =========================================================================*/
    // $('[type="tel"]').inputmask("+38(099)-999-99-99");
    $('[type="tel"]').inputmask("+7(999)-999-99-99");


    /*=========================================================================
        Scroll to top
    =========================================================================*/
    $(document).on('click', '[data-scroll-top]', function () {
        $("html, body").animate({ scrollTop: 0 }, 500);
        return false;
    });


    /*=========================================================================
        Close error by double click
    =========================================================================*/
    $(document).on('dblclick', '.invalid-feedback', function () {
        let $this = $(this);
        let $form = $this.parents('form');
        if ($form) {
            $this.parent('.form-group').find('.invalid-feedback').css('display', 'none');
        }
    });


    /*=========================================================================
        Header collapse
    =========================================================================*/
    const userInfoSelector = '.header-collapse .user-info';
    const headerMessageSelector = '.header-message';
    const $userInfo = $(userInfoSelector);
    const $headerMessage = $(headerMessageSelector);

    $(document).on('click', userInfoSelector, function (event) {
        const $target = $(event.target);

        if ($target.hasClass('user-settings') || $target.parents('.user-settings').length > 0) {
            return;
        }
        $(this).toggleClass('active');
        $(this).find('.user-settings').slideToggle();
    });

    $(document).on('click', headerMessageSelector, function (event) {
        const $target = $(event.target);
    
        if ($target.hasClass('message-info') || $target.parents('.message-info').length > 0) {
            return;
        }
    
        $(this).toggleClass('active');
        $(this).find('.message-info').slideToggle();
    });

    // Обработчик для кликов вне области user-info и header-message
    $(document).on('click', function (event) {
        if (!$userInfo.is(event.target) && $userInfo.has(event.target).length === 0 && $userInfo.hasClass('active')) {
            $userInfo.removeClass('active');
            $userInfo.find('.user-settings').slideUp();
        }

        if (!$headerMessage.is(event.target) && $headerMessage.has(event.target).length === 0 && $headerMessage.hasClass('active')) {
            $headerMessage.removeClass('active');
            $headerMessage.find('.message-info').slideUp();
        }
    });

    // Убираем элемент раскрывающий описание если оно занимает одну строку
    $('.adv-content').each((i, item) => {
        const $item = $(item),
            $toggleLink = $item.find('.adv-content-change'),
            $description = $item.find('.adv-content-inner'),
            description = $description[0],
            height = description.offsetHeight,
            scrollHeight = description.scrollHeight;

        if (scrollHeight - height <= 1) {
            $toggleLink.hide();
        }
    });
})(jQuery);
