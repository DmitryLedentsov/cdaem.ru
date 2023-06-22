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
        console.log('book-button click');
    });

    function convertFormToJSON(form) {
        return form
            .serializeArray()
            .reduce(function (json, { name, value }) {
                json[name] = value;
                return json;
            }, {});
    }
    function getFirstError(message){
        return message[Object.keys(message)[0]][0];
    }
    $(document).on('submit', '#complainForm', function (e) {
        $form = $(this);
        e.preventDefault();

        //TODO: Приделать к бэкенду, отправить на /complaint/{advert_id} поля формы
        const complainStatus={OK:1,ERROR:0};
        console.log("sent: " +JSON.stringify(convertFormToJSON($form)));
        $.post({
            url:$form.attr("action"),
            /*dataType: 'json',*/
            /*contentType: false,*/
            data:convertFormToJSON($form),

            success: function(data) {
                console.log("received: ", data.message);
                if(data.status==complainStatus.OK) {
                    window.toastSuccess(data.message);
                    $("#modalComplain").modal('hide');
                } else {
                    window.openWindow("Ошибка", getFirstError(data.message));
                }
            },

            error: function(data) {
                console.log("error" + data);
                window.openWindow("Ошибка", data.message);
            }
        });
        //TODO: Переделать с использованием ajax.js
        /*window.ajaxRequest($(this), {
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
        });*/
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
