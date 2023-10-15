$(document).ready(function() {

    $('.advert-slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        infinite: false,
        rows: 0,
        asNavFor: '.advert-slider-nav'
    });

    $('.advert-slider-nav').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.advert-slider-for',
        dots: false,
        arrows: false,
        centerMode: false,
        focusOnSelect: true,
        infinite: false,
        rows: 0,
    });

    $('.book-button').click(() => {
        $('#form-reservation').submit();
    });

    $(document).on('submit', '#form-reservation', function (e) {
        console.log("#form-reservation submit");
        e.preventDefault();
        window.ajaxRequest($(this), {
            success: data => {
                if (data.hasOwnProperty('status') && data.status === 'success') {
                    $('#modalBook').modal('hide');
                }
            }
        })
    });

    $(document).on('submit', '#complainForm', function (e) {
        e.preventDefault();
        window.ajaxRequest($(this));
    });

    var advertCoords = [55.76, 37.64];
    ymaps.ready(init);
    function init(){
        var myMap = new ymaps.Map("map", {
            center: advertCoords,
            zoom: 15,
            controls: []
        });

        createMarker(myMap);
        createZoomControls(myMap);
        myMap.behaviors.disable('scrollZoom');

        let keyPressed = false;
        let keyMessageVisible = false;
        let timer;
        const keyMessageSelector = '#ymap_key_display';

        // Отслеживаем скролл мыши на карте, чтобы показывать уведомление
        myMap.events.add(['wheel', 'mousedown'], function(e) {
            if (e.get('type') == 'wheel') {
                if (!keyPressed) { // Ctrl не нажат, показываем уведомление
                    $(keyMessageSelector).fadeIn(300);
                    keyMessageVisible = true;
                    clearTimeout(timer); // Очищаем таймер, чтобы продолжать показывать уведомление
                    timer = setTimeout(function() {
                        $(keyMessageSelector).fadeOut(300);
                        keyMessageVisible = false;
                    }, 1500);
                }
                else { // Ctrl нажат, скрываем сообщение
                    $(keyMessageSelector).fadeOut(100);
                }
            }
            if (e.get('type') == 'mousedown' && keyMessageVisible) { // Скрываем уведомление при клике на карте
                $(keyMessageSelector).fadeOut(100);
            }
        });

        // Обрабатываем нажатие на Клавиши
        $(document).keydown(function(e) {
            // if (e.which === 17 && !ctrlKey) { // Ctrl нажат: включаем масштабирование мышью
            if (e.which === 16 && !keyPressed) { // Shift нажат: включаем масштабирование мышью
                keyPressed = true;
                myMap.behaviors.enable('scrollZoom');
            }
        });
        $(document).keyup(function(e) {
            // if (e.which === 17) { // Ctrl не нажат: выключаем масштабирование мышью
            if (e.which === 16) { // Shift не нажат: выключаем масштабирование мышью
                keyPressed = false;
                myMap.behaviors.disable('scrollZoom');
            }
        });
    }

    function createMarker(myMap) {
        var myPlacemark = new ymaps.Placemark(advertCoords, {}, {
            iconLayout: 'default#image',
            iconImageHref: '/_new/images/icons/apartment/Map-Pin.svg',
            iconImageSize: [80, 80]
        });

        myMap.geoObjects.add(myPlacemark);
    }

    function createZoomControls(myMap){
        const zoomControl = new ymaps.control.ZoomControl({
            options: {
                size: "medium",
                float: 'none',
                position: {
                    bottom: '70px',
                    right: '50px'
                }
            }
        });

        myMap.controls.add(zoomControl);
    }
});
