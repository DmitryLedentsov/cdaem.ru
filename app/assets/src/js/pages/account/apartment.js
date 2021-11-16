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
    $("#address").suggestions({
        token: "79b777f05108f902a4019130a57fe5e7db725cc5",
        type: "ADDRESS",
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            console.log(suggestion);
            console.log(suggestion.value); // если убрать всё до первой запятой включительно, получим адрес без города, так хранится в БД.
            console.log(suggestion.data.city); // название города (можно найти id)
            console.log(suggestion.data.metro); // три ближайших станции
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////
    // Отправка формы          form-apartment
    $(document).on('submit', '#form-apartment', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, {
            success: function (response) {
                // TODO:
                // alert('Success- src/pages/account/apartment.js');
                // console.log(response);

                if (response.status === 'error') {
                    let errors = '';
                    const data = response.data;

                    for (let property in data) {
                        console.log(data[property][0]);
                        errors += data[property][0] + "<br>";
                    }

                    window.openWindow('Ошибка валидации', errors);
                }
            }
        });
    });

})(jQuery);
