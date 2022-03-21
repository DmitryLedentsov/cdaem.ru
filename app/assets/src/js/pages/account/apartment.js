(function ($) {
    "use strict";

    ymaps.ready(init);
    let apartMap;

    const searchCityURL = "/geo/ajax/select-city-by-api/",
        searchAddressURL = "/geo/ajax/select-address-by-api/",
        addressField = $("#address"),
        regionField = $("#region"),
        cityField = $("#city"),
        kladrField = $('#city_kladr');

    function init() {
        apartMap = new ymaps.Map("map", {
            center: [55.76, 37.64],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });

        // Если город уже есть (редактирование) подтягиваем его параметры
        if (cityField.val()) {
            $.getJSON(searchCityURL, {'name': cityField.val()}, function (data) {
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

                cityField.trigger('autocomplete.select', result);
            });
        }
    }


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
                    // console.log(data);
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
        // console.log(item);
        addressField.data('current-value', item.value);

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
                    cityName: cityField.val(),
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
                            $('<option>', { text: station.name, value: station.name }).attr('selected','selected') // TODO передавать через скрытый элемент?
                        );
                    })
                },
                error: function (data) {
                    console.log('error', data);
                }
            });
        }
    });

    addressField.on('blur', function (e, item) {
        let currentDataValue = addressField.data('current-value'),
            isValueChanged = currentDataValue !== addressField.val();

        if (currentDataValue && isValueChanged) {
            addressField.val('');
        }
    });

    function blockAddressField() {
        addressField.prop('disabled', 'disabled');
        addressField.val('');
        regionField.val('');
        kladrField.val('');
        $('#latitude').val('');
        $('#longitude').val('');

        $('.maps').css({display: 'none'});
        $('#address-wrapper').css({display: 'none'});
    }

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

    cityField.on('blur', function (e, item) {
        let currentDataValue = cityField.data('current-value'),
            isValueChanged = currentDataValue !== cityField.val();

        if (currentDataValue && isValueChanged) {
            blockAddressField();
        }
    });

    cityField.on('autocomplete.select', function (evt, item) {
        if (!item.text) {
            blockAddressField();
            return;
        }

        cityField.data('current-value', item.text);

        // центрируем карту по городу
        if (item.geo_lat && item.geo_lon) {
            apartMap.setCenter([item.geo_lat, item.geo_lon]);
            apartMap.setZoom(10);
        }

        const regionFullName = item.region + (item.region_type_full ? (' ' +  item.region_type_full) : '');
        regionField.val(regionFullName);

        kladrField.val(item.value);
        addressField.removeAttr('disabled');
        $('.maps').css({display: 'flex'});
        $('#address-wrapper').css({display: 'block'});

        // console.log(addressField.val());

        if (addressField.val()) {
            $.getJSON(searchAddressURL, {'query': addressField.val(), 'kladr': kladrField.val()}, function (data) {
                // console.log(data);
                let result = [];
                data.forEach(item => {
                    result.push({
                        value: item.value,
                        geo_lat: item.geo_lat,
                        geo_lon: item.geo_lon,
                    });
                });

                // console.log({result});
                addressField.trigger('autocomplete.select', result);
            });
        }

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
