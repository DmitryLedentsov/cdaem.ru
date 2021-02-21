(function ($) {
    "use strict";

    const btnOpenFilter = document.querySelector('.search-form .search-form-filter-extra');
    const btnCloseFilter = document.querySelector('.search-form .btn-close');
    const filter = document.querySelector('.search-form .search-form-filter-options');

    if (!!btnOpenFilter && !!btnCloseFilter) {
        document.addEventListener("click", (e) => {
            if (e.target === btnOpenFilter) return btnOpenFilter.classList.toggle('is-open');
            btnOpenFilter.classList.remove('is-open');
          });
          
          filter.addEventListener("click", (e) => e.stopPropagation());
          btnCloseFilter.addEventListener("click", () => btnOpenFilter.classList.remove('is-open'));
    }

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

})(jQuery);
