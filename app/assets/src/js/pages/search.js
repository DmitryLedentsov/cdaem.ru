(function ($) {
    "use strict";
  
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
    }
  
})(jQuery);