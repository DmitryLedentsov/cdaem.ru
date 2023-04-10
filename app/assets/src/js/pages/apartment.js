(function ($) {
    "use strict";

    $('.advert-slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        infinite: false,
        rows: 0,
        asNavFor: '.advert-slider-nav'
    });

    $('.advert-slider-nav').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.advert-slider-for',
        dots: false,
        arrows: false,
        centerMode: false,
        focusOnSelect: true,
        infinite: false,
        rows: 0,
    });

    $('.book-button').click(() => {
        console.log('book-button click');
    });

})(jQuery);
