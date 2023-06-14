(function ($)  {

    // tooltip info close contact
    $(document).on('click', '.adv-card .adv-card-safe-icon', function () {
        $(this).find('.adv-card-safe-tooltip').toggleClass('adv-card-safe-tooltip-active');
    });

    // change price value
    $(document).on('click', '.adv-card .adv-card-advs-price-value', function (e) {
        e.preventDefault();
        let $this = $(this);
        $this.closest('.adv-card-advs-price-item').siblings().find('.adv-card-advs-modal').removeClass('adv-card-advs-modal-active');
        $this.closest('.adv-card-advs-price-item').find('.adv-card-advs-modal').toggleClass('adv-card-advs-modal-active');
    });

    $(document).on('submit', '.adv-form', function (e) {
        e.preventDefault();
        // console.log('submit');

        // ни один метод не вызывается, если возвращаем не 200
        window.ajaxRequest($(this), {
            success: function (response) {
                console.log('success');
                console.log(response);
            },
            error: function (data) {
                console.log('error');
                console.log(data);
            },
            fail: function (data) {
                console.log('fail');
                console.log(data);
            }
        });
    });

    // content active
    $(document).on('click', '.adv-card .adv-content-change, .adv-card .adv-content-inner', function () {
        let $this = $(this);
        $('.adv-card .adv-content-change').toggleClass('adv-content-change-active');
        $('.adv-card .adv-content-inner').toggleClass('adv-content-inner-active');
    });


    // open contact phone
    $(document).on('click', '.adv-card .adv-card-contact-number, .adv-card .adv-card-contact-change', function (e) {
        e.preventDefault();
        let $this = $(this);
        $('.adv-card .adv-card-contact-change').toggleClass('adv-card-contact-change-active');
        $this.closest('.adv-card-contact-phone').find('.adv-card-phones').toggleClass('adv-card-phones-active');
    });

    // Посмотреть на карте
    $(document).on('click', '.adv-card-metro-link', function (e) {
        let $this = $(this),
            data = $this.data();

        const mapDiv = $('<div>', {class: 'adv-card'}).append(
            $('<div>', {id: 'apart-map', class: 'maps-frame'})
        );

        ymaps.ready(init);
        let apartMap;

        function init() {
            apartMap = new ymaps.Map("apart-map", {
                center: [data.latitude, data.longitude],
                zoom: 15
            }, {
                searchControlProvider: 'yandex#search'
            });

            apartMap.geoObjects.add(new ymaps.Placemark([data.latitude, data.longitude], {
                balloonContent: ''
            }, {
                preset: 'islands#icon',
                iconColor: '#0095b6'
            }));

        }

        window.openWindow(data.address, mapDiv, 'large');
    });


    function updateContactStatusAndReturn(apartmentId, status) {
        $.get(`/partners/office/update-contact-status?id=${apartmentId}&status=${status}`, function () {
            location.href = '/office/apartments';
        });
    }

    $(document).on('click', '#close-contact-button', function (e) {
        const apartmentId = $(this).data('apartmentId');
        updateContactStatusAndReturn(apartmentId, 0);
    });

    $(document).on('click', '#open-contact-button', function (e) {
        const apartmentId = $(this).data('apartmentId');
        updateContactStatusAndReturn(apartmentId, 1);
    });

})(jQuery);