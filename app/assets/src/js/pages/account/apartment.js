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

        // todo тесовый маркер для обозначения адреса
        apartMap.geoObjects.add(new ymaps.Placemark([55.684758, 37.738521], {
            balloonContent: ''
        }, {
            preset: 'islands#icon',
            iconColor: '#0095b6'
        }));
    }
    // Интеграция с dadata.ru
    const apiToken = "79b777f05108f902a4019130a57fe5e7db725cc5";
    const addressField = $("#address");
    const regionField = $("#region");
    const cityField = $("#city");

    $('.maps-link-item').click(function () {
        // console.log();
        const point = $(this).data();
        const cityName = $(this).html();
        cityField.val(cityName);
        cityField.suggestions().fixData();
        addressField.val('');
        addressField.suggestions().fixData();
        apartMap.setCenter([point.latitude, point.longitude]);
    });

    addressField.suggestions({
        token: apiToken,
        type: "ADDRESS",
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            console.log(suggestion);
            console.log(suggestion.value); // если убрать всё до первой запятой включительно, получим адрес без города, так хранится в БД.
            console.log(suggestion.data.city); // название города (можно найти id)
            console.log(suggestion.data.metro); // три ближайших станции // только для тарифа «максимальный» 36К в год

            // центрируем карту по адресу
            const point = {
                lat: suggestion.data.geo_lat,
                lon: suggestion.data.geo_lon
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


                $.ajax({
                    url: '/geo/nearest-stations',
                    type: 'GET',
                    data: {
                        cityId: 4400,
                        latitude: point.lat,
                        longitude: point.lon
                    },
                    success: function (data) {
                        console.log('success', data);

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
        }
    }).prop("disabled", "disabled");

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
        addressField.suggestions().setOptions({
            restrict_value: false
        });
        addressField.prop('disabled', 'disabled');
        addressField.val('');
        regionField.val('');

        $('.maps').css({display: 'none'});
        $('#address-wrapper').css({display: 'none'});
    }

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
        /* Вызывается, когда пользователь выбирает одну из подсказок */
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
