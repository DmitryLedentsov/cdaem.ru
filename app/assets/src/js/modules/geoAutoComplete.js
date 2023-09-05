(function ($) {

    /*=========================================================================
        City AutoComplete
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
                    $.getJSON($('#search-form-autocomplete').data('url'), {'name': qry}, function (response) {
                        let result = [];
                        let data = response.data;
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