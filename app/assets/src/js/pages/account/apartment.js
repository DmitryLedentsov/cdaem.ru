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


    ///////////////////////////////////////////////////////////////////////////////////
    // Отправка формы
    $(document).on('submit', '#form-apartment', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, {
            success: function (response) {

                // TODO:
                alert('Success- src/pages/account/apartment.js');

            }
        });
    });

})(jQuery);