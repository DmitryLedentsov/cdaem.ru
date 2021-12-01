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

    $('.maps-link-item').click(function () {
        // console.log();
        const point = $(this).data();
        apartMap.setCenter([point.latitude, point.longitude]);
    });

    // Интеграция с dadata.ru
    const apiToken = "79b777f05108f902a4019130a57fe5e7db725cc5";
    const addressField = $("#address");

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
