$(document).ready(function () {

    if ($('#liveYmap').length > 0) {
        LiveYmap();
    }

    if ($('#map').length) {
        Ymap();
    }

});


function Ymap() {

    var myMap,
        myPlacemark;

    var address = $('#map').data('address');

    function init() {
        ymaps.geocode(address, {results: 1}).then(function (res) {

            var firstGeoObject = res.geoObjects.get(0);

            myMap = new ymaps.Map("map", {     //Новый обьект класса ymaps.Map  айдишник контейнера во вьюшке "map"
                center: firstGeoObject.geometry.getCoordinates(),         //Сюда нужно вводить центр карты(еще не знаю всех возможностей)
                zoom: 17  // Просто увеличить карту
            });

            myMap.controls.add('zoomControl', {left: 5, top: 5}).add('typeSelector');  //Добавляет ЗумКонтрлллер и Тип карт

            myPlacemark = new ymaps.Placemark(firstGeoObject.geometry.getCoordinates(), {   // Пимпочка которая находится на карте
                //hintContent: 'Москва!', // Типа в этой пимпочке тайтл и текст
                //balloonContent: 'Столица России'
            });

            myMap.geoObjects.add(myPlacemark); // Приписали обьекту myMap свойство myPlacemark
        }, function (err) {
            // Если геокодирование не удалось, сообщаем об ошибке.
            console.log(err.message);
        })
    }

    ymaps.ready(init);
}







function LiveYmap() {

    var mapTag = $('#liveYmap');
    var cityTag = $('#' + mapTag.data('city-tag'));
    var addressTag = $('#' + mapTag.data('address-tag'));
    //var cityWantPass = mapTag.data('city-name');
    var address;
    var myMap;

    function init() {
        if ((cityTag.val() || cityTag.html()) && cityTag.html() != 'Выбрать') {
            addressTag.blur();
        } else {
            ymaps.geocode(ymaps.geolocation.city, {results: 1}).then(function (res) {

                var firstGeoObject = res.geoObjects.get(0);

                myMap = new ymaps.Map("liveYmap", {
                    center: firstGeoObject.geometry.getCoordinates(),
                    zoom: 12
                });

                myMap.controls.add('zoomControl', {left: 5, top: 5}).add('typeSelector');
            }, function (err) {
                // Если геокодирование не удалось, сообщаем об ошибке.
                alert(err.message);
            })
        }
    }

    function onBlur() {

        if (address == this.value) return;
        if (!this.value) return;
        mapTag.empty();
        address = this.value;
        var fullAddress = '';

        if (cityTag.length > 0) {
            if (cityTag.val()) {
                fullAddress = cityTag.val();
            } else {
                fullAddress = cityTag.html();
            }
        } else {
            /*if (cityWantPass) {
                fullAddress = cityWantPass;
            }*/
        }
        fullAddress += ' ' + address;

         console.log(fullAddress);

        ymaps.geocode(fullAddress, {results: 1}).then(function (res) {

            var firstGeoObject = res.geoObjects.get(0);

            var coordinates;
            if (firstGeoObject) {
                coordinates = firstGeoObject.geometry.getCoordinates();
            } else {
                init();
                return;
            }

            myMap = new ymaps.Map("liveYmap", {
                center: coordinates,
                zoom: 17
            });

            myMap.controls.add('zoomControl', {left: 5, top: 5}).add('typeSelector');  //Добавляет ЗумКонтрлллер и Тип карт

            myPlacemark = new ymaps.Placemark(firstGeoObject.geometry.getCoordinates(), {   // Пимпочка которая находится на карте
                //hintContent: 'Москва!',                           // Типа в этой пимпочке тайтл и текст
                //balloonContent: 'Столица России'
            });

            myMap.geoObjects.add(myPlacemark);         // Приписали обьекту myMap свойство myPlacemark
        }, function (err) {
            // Если геокодирование не удалось, сообщаем об ошибке.
            alert(err.message);
        })
    }

    addressTag.on('blur', onBlur);

    ymaps.ready(init);
}