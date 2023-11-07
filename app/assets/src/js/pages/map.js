
(function ($) {
    ymaps.ready(init);

    function init() {
        var $map = $("#map");
        var $searchForm = $("#search");
        var myMap = new ymaps.Map('map', {
                center: [$map.data("latitude") , $map.data("longitude")],
                zoom: 10
            }),
            objectManager = new ymaps.ObjectManager({
                clusterize: true,
                gridSize: 32
            });
        myMap.behaviors.disable('scrollZoom');

        let keyPressed = false;
        let keyMessageVisible = false;
        let timer;
        const keyMessageSelector = '#ymap_key_display';

        // Отслеживаем скролл мыши на карте, чтобы показывать уведомление
        myMap.events.add(['wheel', 'mousedown'], function(e) {
            if (e.get('type') === 'wheel') {
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
            if (e.get('type') === 'mousedown' && keyMessageVisible) { // Скрываем уведомление при клике на карте
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

        // Задаём опции кластеров
        objectManager.clusters.options.set({
            preset: 'islands#blueClusterIcons',
            //balloonContentLayout: 'cluster#balloonCarousel',
            //clusterBalloonPagerSize: 5,
            //clusterBalloonPanelMaxMapArea: 0,
            balloonContentLayoutWidth: 400,
            balloonContentLayoutHeight: 300
        });

        var activeObjectMonitor = new ymaps.Monitor(objectManager.clusters.state);

        // При клике на некластеризованные объекты получаем содержимое балуна
        objectManager.objects.events.add('click', function (e) {
            var objectId = e.get('objectId');
            getBalloonData(objectId, function (data) {
                render(data, objectId);
            });
        });

        // В кластеризованных объектах отслеживаем изменение выбранного объекта
        activeObjectMonitor.add('activeObject', function () {
            var objectId = activeObjectMonitor.get('activeObject').id;
            getBalloonData(objectId, function (data) {
                render(data, objectId);
            });
        });

        // Проверяем, есть ли у выбранного объекта содержимое балуна
        /*function hasBalloonData(objectId) {
         return objectManager.objects.getById(objectId).properties.balloonContent;
         }*/


        // Получаем данные и устанавливаем содержимое балуна
        /*function setBalloonData(objectId) {
         if (!hasBalloonData(objectId)) {
         getBalloonData(objectId).done(function (data) {
         var object = objectManager.objects.getById(objectId);
         object.properties.balloonContent = data;
         var objectState = objectManager.getObjectState(objectId);
         if (objectState.isClustered) {
         objectManager.clusters.balloon.open(objectState.cluster.id);
         }
         else {
         objectManager.objects.balloon.open(objectId);
         }
         });
         }
         }*/


        myMap.geoObjects.add(objectManager);
        function queryMapObjects(){
            $.ajax({
                url: $searchForm.data('url'),
                dataType: 'json',
                method: 'GET',
                data: Object.fromEntries(new FormData($searchForm[0])),
            })
                .done(function (response) {
                    objectManager.add(response.data);
                });
        }
        //let $formData = new FormData($searchForm[0]);
        /*$searchForm.submit(function(e){
            e.preventDefault();
            console.log(new FormData($searchForm[0]));
            queryMapObjects();
            window.location.href=$searchForm.attr("action");
        });*/
        queryMapObjects();


        // Функция, осуществляющая запрос за данными балуна на сервер.
        function getBalloonData(objectId, done) {
            var dataDeferred = ymaps.vow.defer();
            $.ajax({
                url: $searchForm.data('url') + objectId,
                type: 'GET',
                dataType: 'html'
            })
                .done(function (data) {
                    done(data);
                    dataDeferred.resolve(data);
                })
                .fail(function () {
                    dataDeferred.resolve('error');
                });
        }


        function render(data, objectId) {
            var object = objectManager.objects.getById(objectId);
            object.properties.balloonContent = data;
            var objectState = objectManager.getObjectState(objectId);
            if (objectState.isClustered) {
                objectManager.clusters.balloon.open(objectState.cluster.id);
            } else {
                objectManager.objects.balloon.open(objectId);
            }
        }
    }
})(jQuery);
