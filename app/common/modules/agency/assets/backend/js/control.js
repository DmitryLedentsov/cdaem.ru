$(function () {

    /**
     * Действия к изображениям
     * @type {{defaultImage: Function, deleteImage: Function}}
     */
    var actions = {
        defaultImage: function (response, element) {
            var controlImages = $("div#controlImages");
            //Снять выделение
            var oldDefault = controlImages.children('.thumbnail.active.thumbnail-boxed');
            oldDefault.attr('class', 'thumbnail thumbnail-boxed');
            //Выделить
            controlImages.children('#' + response.id).attr('class', 'thumbnail active thumbnail-boxed');
            //Перемещение кнопки
            element.attr('href', element.attr('href').replace(/id=[0-9]+/, 'id=' + oldDefault.attr('id')));
            element.prependTo(oldDefault.find('.thumb-options').find('span'));
        },
        deleteImage: function (response, element) {
            var controlImages = $("div#controlImages");
            var image = controlImages.children('#' + response.id);
            image.remove();
            if (response.newDefaultImg) {
                var newDefaultImg = controlImages.children('#' + response.newDefaultImg)
                newDefaultImg.attr('class', 'thumbnail active thumbnail-boxed');
                newDefaultImg.find('.thumb-options').find('i.icon-lamp2').parent().remove();
            }
        }
    };


    /**
     * Действия к изображениям
     */
    $("#controlImages a").on('click', function (event) {
        event.preventDefault();
        var $this = $(this);

        if ($this.hasClass('update-image-btn')) {
            // Здесь немного другой подход
            $('#imageModal').modal('show')
                .find('#modal-image-content')
                .load($this.attr('href'));
        } else {
            $.ajax({
                type: 'GET',
                cache: false,
                url: $this.attr('href'),
                success: function (response) {
                    actions[response.action](response, $this);
                }
            });
        }
    });


    /**
     * Сортировка изображений
     */
    $('#controlImages').sortable({
        update: function (event, ui) {
            var $this = $(this);
            var url = $this.data('sort-url');
            $.ajax({
                data: {sort: $this.sortable("toArray")},
                type: 'POST',
                url: url
            });
        }
    });

});