(function ($) {
    "use strict";

    /*=========================================================================
        Seo text
    =========================================================================*/
    $(document).on('click', '[data-seo-text]', function () {
        $(this).parent().toggleClass('txt-show');
    });


    /*=========================================================================
        Filter nav
    =========================================================================*/
    const btnOpenFilter = document.querySelector('.search-form .search-form-filter-extra');
    const btnCloseFilter = document.querySelector('.search-form .btn-close');
    const filter = document.querySelector('.search-form .search-form-filter-options');

    if (!!btnOpenFilter && !!btnCloseFilter) {
        document.addEventListener("click", (e) => {
            if (e.target === btnOpenFilter) {
                return btnOpenFilter.classList.toggle('is-open');
            }
            btnOpenFilter.classList.remove('is-open');
        });

        filter.addEventListener("click", (e) => e.stopPropagation());
        btnCloseFilter.addEventListener("click", () => btnOpenFilter.classList.remove('is-open'));
    }


    /*=========================================================================
        AutoComplete
    =========================================================================*/
    if (typeof ($(document).autoComplete) === typeof (Function)) {
        let $searchFormAutocomplete = $('#search-form-autocomplete');
        $searchFormAutocomplete.autoComplete({
            resolver: 'custom',
            minLength: 2,
            noResultsText: 'Город не найден',
            formatResult: function (item) {
                return {
                    value: item.text,
                    text: item.text,
                    html: '<div>' + item.text + '</div>',
                };
            },
            events: {
                search: function (qry, callback) {
                    $.getJSON($('#search-form-autocomplete').data('url'), {'name': qry}, function (data) {
                        let result = [];
                        for (let i in data) {
                            result.push({
                                value: data[i].name_eng,
                                text: data[i].name,
                            });
                        }
                        callback(result);
                    });
                },
            }
        });
        $searchFormAutocomplete.on('autocomplete.select', function (evt, item) {
            $('#search-form-autocomplete-selected').val(item.value);
        });
    }

})(jQuery);