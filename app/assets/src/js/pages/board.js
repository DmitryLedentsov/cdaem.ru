(function ($) {
  "use strict";

    $('.top-advert-slider').slick({
        centerMode: true,
        slidesToShow: 1,
        arrows: true,
        dots: false,
        infinite: true,
        variableWidth: true,
    });

    if (typeof($(document).autoComplete) === typeof(Function)) {
        $('#search-form-autocomplete').autoComplete({
            resolver: 'custom',
            formatResult: function (item) {
                return {
                    value: item.text,
                    text: item.text,
                    html: '<div>' + item.text + '</div>',
                };
            },
            events: {
                search: function (qry, callback) {
                    callback([
                        { "value": 1, "text": "Москва" },
                        { "value": 2, "text": "Самара" },
                        { "value": 3, "text": "Санкт-Петербург" },
                        { "value": 4, "text": "Екатеринбург" },
                        { "value": 5, "text": "Другой город 1" },
                        { "value": 6, "text": "Другой город 2" },
                        { "value": 7, "text": "Другой город 3" },
                        { "value": 8, "text": "Другой город 4" },
                        { "value": 9, "text": "Другой город 5" },
                        { "value": 10, "text": "Другой город 6" },
                        { "value": 11, "text": "Другой город 7" }
                    ])
                },
            }
        });
    
        $('#lookup-board-form-autocomplete').autoComplete({
            resolver: 'custom',
            formatResult: function (item) {
                return {
                    value: item.text,
                    text: item.text,
                    html: '<div>' + item.text + '</div>',
                };
            },
            events: {
                search: function (qry, callback) {
                    callback([
                        { "value": 1, "text": "Метро" },
                        { "value": 2, "text": "Югозападная" },
                        { "value": 3, "text": "Алма-Ата" },
                        { "value": 4, "text": "Улица" },
                        { "value": 5, "text": "Югозападная улица" },
                        { "value": 6, "text": "Юговая улица" },
                        { "value": 7, "text": "Западная улица" },
                        { "value": 8, "text": "Южная улица" },
                        { "value": 9, "text": "Другая улица" },
                    ])
                },
            }
        });
    }
    
    function setChecked(target, type) {
        var checked = $(target).find("input[type='checkbox']:checked").length;
        if (checked) {
            $(target).find('select option:first').html('Выбрано: ' + checked + ' удобств(а)');
        } else {
            $(target).find('select option:first').html('Удобства*');
        }
    }
    
    $.fn.checkselect = function() {
        this.wrapInner('<div class="checkselect-popup"></div>');
        this.prepend(
          '<div class="checkselect-control">' +
          '<select class="form-control custom-select"><option></option></select>' +
          '<div class="checkselect-over"></div>' +
          '</div>'
        );
        
        this.each(function(){
            setChecked(this);
        });
        this.find('input[type="checkbox"]').click(function(){
            setChecked($(this).parents('.checkselect'));
        });
        
        this.parent().find('.checkselect-control').on('click', function(){
            var $popup = $(this).next();
            $('.checkselect-popup').not($popup).css('display', 'none');
            if ($popup.is(':hidden')) {
                $popup.css('display', 'block');
                $(this).find('select').focus();
            } else {
                $popup.css('display', 'none');
            }
        });
        
        $('html, body').on('click', function(e){
            if ($(e.target).closest('.checkselect').length == 0){
                $('.checkselect-popup').css('display', 'none');
            }
        });
    };

})(jQuery);

$('.checkselect').checkselect();
