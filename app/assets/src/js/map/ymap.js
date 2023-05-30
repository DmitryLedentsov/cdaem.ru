(function ($) {
    var advertCoords = [55.76, 37.64];

    ymaps.ready(init);
    function init(){
        var myMap = new ymaps.Map("map", {
            center: advertCoords,
            zoom: 15,
            controls: []
        });
        createMarker(myMap);
    }

    function createMarker(myMap) {
        var myPlacemark = new ymaps.Placemark(advertCoords, {}, {
            iconLayout: 'default#image',
            iconImageHref: './images/icons/apartment/mapMarker.svg',
            iconImageSize: [32, 30]
        });

        myMap.geoObjects.add(myPlacemark);
    }

})(jQuery);