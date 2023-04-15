jQuery(function () {
    console.log('top-slider.js - dev');

    /**
     * Выбор типа и метки рекламы
     */
    var advType = 1;
    var advertisementformLabelOptions = $('#advertisementsliderform-label').find('option');

    /**
     * Модальное окно с выбором объявления
     */
    $(document).on("click", '#top-slider', function (event) {
        event.preventDefault();
        var $this = $(this);
        var service = $this.data('service');
        $.get("/partners/ajax/realty-objects-by-service?service=" + service, function (response) {
            // $('#modal-realty-objects-by-service').remove();
            // $('body').append(response);
            // $('#modal-realty-objects-by-service').modal('show');

            console.log('new modal');

            window.openWindow("Выбор объявления", response, 'large');
        });
    });



    $(document).on('submit', '#form-top-slider', function (e) {
        e.preventDefault();
        window.ajaxRequest($(this));
    });




    /**
     * Выбор объявления
     */
    $(document).on("click", '#modal-realty-objects-by-service .advert-preview', function (event) {
        event.preventDefault();
        var $this = $(this);
        $('#advertisementsliderform-advert_id').val($this.data('advert'));
        $('#top-slider').html($this.parent().html());
        $('#modal-realty-objects-by-service').modal('hide');
    });


    /**
     * Обрабатываем зависимости списков
     */
    $('#advertisementsliderform-type').on('change', function () {
        var selectedValue = $(this).find("option:selected").val();

        advType = selectedValue;

        if (parseInt(advType) === 1) {
            $('#group-add-in-top').show();
            $('#advertisementsliderform-label').val(1);
        } else if (parseInt(advType) === 2) {
            $('#group-add-in-top').hide();
            $('#advertisementsliderform-label').val(2);
        } else if (parseInt(advType) === 3) {
            $('#group-add-in-top').hide();
            $('#advertisementsliderform-label').val(4);
        }

        var options = $('#advertisementsliderform-label').html(advertisementformLabelOptions).find('option');

        // Хочу снять
        if (parseInt(advType) === 3) {
            options.each(function (index, value) {
                var targetOption = $(value);
                if (parseInt(targetOption.val()) !== 4) {
                    targetOption.remove();
                }
            });
        }
        // Eсть клиент
        else if (advType === 2) {
            options.each(function (index, value) {
                var targetOption = $(value);
                if (parseInt(targetOption.val()) !== 2) {
                    targetOption.remove();
                }
            });
        }
        // Сдам
        else if (advType === 1) {
            options.each(function (index, value) {
                var targetOption = $(value);
                if (parseInt(targetOption.val()) === 2 || parseInt(targetOption.val()) === 4) {
                    targetOption.remove();
                }
            });
        }

        $('#advertisementsliderform-label').val(1);
    });

});
