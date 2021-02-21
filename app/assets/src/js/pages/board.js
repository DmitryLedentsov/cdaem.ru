(function ($) {
  "use strict";

  $('.recommendation-slider').slick({
    centerMode: false,
    slidesToShow: 1,
    arrows: true,
    dots: false,
    infinite: false,
    variableWidth: true,
  });

  $('.top-advert-slider').slick({
    centerMode: true,
    slidesToShow: 1,
    arrows: true,
    dots: false,
    infinite: true,
    variableWidth: true,
  });

})(jQuery);