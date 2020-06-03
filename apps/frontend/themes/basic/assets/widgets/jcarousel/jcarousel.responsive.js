(function($) {
    $(function() {
        var jcarousel = $('.jcarousel'),
            carouselScroll;

        autoScroll(4000); // Запуск автоскролла

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = jcarousel.innerWidth();

                /*if (width >= 600) {
                    width = width / 7;
                } else if (width >= 350) {
                    width = width / 4;
                }*/

                width = 140;
                jcarousel.jcarousel('items').css('width', width + 'px');
            })
            .mouseover(function() { // При наведении курсора остановить прокрутку
                clearInterval(carouselScroll);
            })
            .mouseout(function() { // При потере фокуса запускать прокрутку
                autoScroll(4000); // Запуск автоскролла
            })
            .jcarousel({
                wrap: 'circular',
                animation: 3000
            });

        $('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

        $('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });

        // Tooltips
        jcarousel.find('li')
            .mouseover(function() {
                var block = $(this).find('.jcarousel-title'),
                    parentBlock = block.parent(),
                    contentBlock = block.find('div');

                block.show();
                contentBlock.css('height','100px'); // Установить высоту блока такую же, как и у родительского эл-та
            })
            .mouseout(function() {
                var block = $(this).find('.jcarousel-title');

                block.hide();
            });

        // Функция запуска автоскролла
        function autoScroll(interval) {
            carouselScroll = setInterval(function() {
                    jcarousel.data('jcarousel').scroll('+=1');
                }, interval
            );
        }

    });
})(jQuery);