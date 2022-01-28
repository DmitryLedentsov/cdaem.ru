(function ($) {
    "use strict";

    ymaps.ready(init);
    let apartMap;

    function init() {
        apartMap = new ymaps.Map("map", {
            center: [55.76, 37.64],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });
    }

    const searchCityURL = "/geo/ajax/select-city-by-api/",
        searchAddressURL = "/geo/ajax/select-address-by-api/",
        addressField = $("#address"),
        regionField = $("#region"),
        cityField = $("#city"),
        kladrField = $('#city_kladr');

    $('.maps-link-item').click(function () {
        const point = $(this).data();
        const cityName = $(this).html();
        cityField.val(cityName);
        addressField.val('');
        apartMap.setCenter([point.latitude, point.longitude]);
    });

    addressField.prop("disabled", "disabled");

    addressField.autoComplete({
        resolver: 'custom',
        minLength: 2,
        noResultsText: 'Адрес не найден',
        formatResult: function (item) {
            return {
                value: item.value,
                text: item.value,
                html: '<div>' + item.value + '</div>',
            };
        },
        events: {
            search: function (qry, callback) {
                $.getJSON(searchAddressURL, {'query': qry, 'kladr': kladrField.val()}, function (data) {
                    console.log(data);
                    let result = [];
                    data.forEach(item => {
                        result.push({
                            value: item.value,
                            geo_lat: item.geo_lat,
                            geo_lon: item.geo_lon,
                        });
                    });

                    // console.log({result});
                    callback(result);
                });
            },
        }
    });

    addressField.on('autocomplete.select', function (evt, item) {
        console.log(item);
        // центрируем карту по адресу
        const point = {
            lat: item.geo_lat,
            lon: item.geo_lon
        };

        if (point.lat && point.lon) {
            apartMap.setCenter([point.lat, point.lon]);
            apartMap.setZoom(15);

            apartMap.geoObjects.add(new ymaps.Placemark([point.lat, point.lon], {
                balloonContent: ''
            }, {
                preset: 'islands#icon',
                iconColor: '#0095b6'
            }));

            $('#latitude').val(point.lat);
            $('#longitude').val(point.lon);

            $.ajax({
                url: '/geo/nearest-stations',
                type: 'GET',
                data: {
                    cityId: 4400,
                    latitude: point.lat,
                    longitude: point.lon
                },
                success: function (data) {
                    // console.log('success', data);
                    const metroBlock = $('.metro-block');

                    if (!data.length) {
                        metroBlock.hide();
                        return;
                    }

                    metroBlock.show();
                    const metroListSelect = $('#metro-list');
                    metroListSelect.html('');

                    data.forEach((station) => {
                        metroListSelect.append(
                            $('<option>', { text: station.name })
                        );
                    })
                },
                error: function (data) {
                    console.log('error', data);
                }
            });
        }
    });



    // поиск только по городам
    var defaultFormatResult = $.Suggestions.prototype.formatResult;

    function formatResult(value, currentValue, suggestion, options) {
        var newValue = suggestion.data.city;
        suggestion.value = newValue;
        return defaultFormatResult.call(this, newValue, currentValue, suggestion, options);
    }

    function formatSelected(suggestion) {
        return suggestion.data.city;
    }

    function blockAddressField() {
        // addressField.suggestions().setOptions({
        //     restrict_value: false
        // });
        addressField.prop('disabled', 'disabled');
        addressField.val('');
        regionField.val('');
        kladrField.val('');
        $('#latitude').val('');
        $('#longitude').val('');

        $('.maps').css({display: 'none'});
        $('#address-wrapper').css({display: 'none'});
    }

    /*
    $("#city").suggestions({
        token: apiToken,
        type: "ADDRESS",
        hint: false,
        bounds: "city",
        constraints: {
            locations: { city_type_full: "город" }
        },
        formatResult: formatResult,
        formatSelected: formatSelected,
        //  Вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            console.log(suggestion);
            console.log(suggestion.data.city);

            if (suggestion.data && suggestion.data.kladr_id) {
                addressField.removeAttr('disabled');
                addressField.suggestions().setOptions({
                    constraints: {
                        locations: { kladr_id: suggestion.data.kladr_id }
                    },
                    restrict_value: true
                });

                // центрируем карту по городу
                if (suggestion.data.geo_lat && suggestion.data.geo_lon) {
                    apartMap.setCenter([suggestion.data.geo_lat, suggestion.data.geo_lon]);
                    apartMap.setZoom(10);
                }

                $('.maps').css({display: 'flex'});
                $('#address-wrapper').css({display: 'block'});

                // устанвливаем значение региона
                const regionFullName = suggestion.data.region + (suggestion.data.region_type_full ? (' ' +  suggestion.data.region_type_full) : '');
                console.log(regionFullName);
                regionField.val(regionFullName);
            }
            else {
                blockAddressField();
            }
        },
        onSelectNothing: function () {
            blockAddressField();
        }
    });
    */
    cityField.autoComplete({
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
        change: function (event, ui) {
            console.log("change");
            if (ui.item === null) {
                blockAddressField();
            }
        },

        events: {
            change: function (event, ui) {
                console.log("events.change");
                if (ui.item === null) {
                    blockAddressField();
                }
            },
            search: function (qry, callback) {
                $.getJSON(searchCityURL, {'name': qry}, function (data) {
                    // console.log(data);
                    let result = [];
                    data.forEach(item => {
                        result.push({
                            value: item.kladr_id,
                            text: item.name,
                            region: item.region,
                            region_type_full: item.region_type_full,
                            geo_lat: item.geo_lat,
                            geo_lon: item.geo_lon,
                        });
                    });

                    // console.log({result});
                    callback(result);
                });
            },
        }
    });

     cityField.on('autocomplete.change', function (evt, item) {
        console.log("autocomplete.change", item);
    });

    cityField.on('autocomplete.Change', function (evt, item) {
        console.log("autocomplete.Change", item);
    });

    cityField.on('autocompleteChange', function (evt, item) {
        console.log("autocompleteChange", item);
    });

    cityField.bind('autocomplete.change', function (evt, item) {
        console.log("bind autocomplete.change", item);
    });

    cityField.bind('autocomplete.Change', function (evt, item) {
        console.log("bind autocomplete.Change", item);
    });

    cityField.bind('autocompleteChange', function (evt, item) {
        console.log("bind autocompleteChange", item);
    });

    cityField.on('blur', function (e, item) {
        // console.log(e, item, cityField);
        // Работает только этот метод
        if(!cityField.data("autoComplete")._isSelectElement) {
            blockAddressField();
        }
    });


    cityField.on('autocomplete.select', function (evt, item) {
        // console.log(item);
        if (!item.text) {
            blockAddressField();
            return;
        }

        // центрируем карту по городу
        if (item.geo_lat && item.geo_lon) {
            apartMap.setCenter([item.geo_lat, item.geo_lon]);
            apartMap.setZoom(10);
        }

        const regionFullName = item.region + (item.region_type_full ? (' ' +  item.region_type_full) : '');
        // console.log({regionFullName});
        regionField.val(regionFullName);

        kladrField.val(item.value);
        addressField.removeAttr('disabled');
        $('.maps').css({display: 'flex'});
        $('#address-wrapper').css({display: 'block'});
    });


    ///////////////////////////////////////////////////////////////////////////////////
    // Отправка формы          form-apartment
    $(document).on('submit', '#form-apartment', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, {
            success: function (response) {

            }
        });
    });

})(jQuery);
