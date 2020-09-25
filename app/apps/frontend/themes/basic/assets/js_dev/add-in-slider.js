jQuery(function () {

    /**
     * Выбор типа и метки рекламы
     */
    var advType = 1;
    var advertisementformLabelOptions = $('#advertisementsliderform-label').find('option');

    $('#advertisementsliderform-label').find('option[value=2]').remove();
    $('#advertisementsliderform-label').find('option[value=4]').remove();
    $('#advertisementsliderform-label').selectpicker('refresh');


    /**
     * Модальное окно с выбором объявления
     */
    $(document).on("click", '#add-in-slider', function (event) {
        event.preventDefault();
        var $this = $(this);
        var service = $this.data('service');
        $.get("/partners/ajax/realty-objects-by-service?service=" + service, function (response) {
            $('#modal-realty-objects-by-service').remove();
            $('body').append(response);
            $('#modal-realty-objects-by-service').modal('show');
        });
    });


    /**
     * Отправка формы "Разместить рекламное объявление"
     */
    $('#form-buy-ads').formApi({

        // Все поля
        fields: [
            "_csrf",
            "AdvertisementSliderForm[type]",
            "AdvertisementSliderForm[label]",
            "AdvertisementSliderForm[more_info]",
            "AdvertisementSliderForm[advert_id]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "advertisementsliderform-type",
            "advertisementsliderform-label",
            "advertisementsliderform-more_info",
            "advertisementsliderform-advert_id"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

        }
    });


    /**
     * Выбор объявления
     */
    $(document).on("click", '#modal-realty-objects-by-service .advert-preview', function (event) {
        event.preventDefault();
        var $this = $(this);
        $('#advertisementsliderform-advert_id').val($this.data('advert'));
        $('#add-in-slider').html($this.parent().html());
        $('#modal-realty-objects-by-service').modal('hide');
    });


    /**
     * Обрабатываем зависимости списков
     */
    $('#advertisementsliderform-type').on('change', function () {
        var selectedValue = $(this).find("option:selected").val();

        advType = selectedValue;

        if (advType == 1) {
            $('#group-add-in-top').show();
            $('#advertisementsliderform-label').val(1).selectpicker('refresh');
        } else if (advType == 2) {
            $('#group-add-in-top').hide();
            $('#advertisementsliderform-label').val(2).selectpicker('refresh');
        } else if (advType == 3) {
            $('#group-add-in-top').hide();
            $('#advertisementsliderform-label').val(4).selectpicker('refresh');
        }

        var options = $('#advertisementsliderform-label').html(advertisementformLabelOptions).find('option');

        // Хочу снять
        if (advType == 3) {
            options.each(function (index, value) {
                var targetOption = $(value);
                if (targetOption.val() != 4) {
                    targetOption.remove();
                }
            });
        }
        // Eсть клиент
        else if (advType == 2) {
            options.each(function (index, value) {
                var targetOption = $(value);
                if (targetOption.val() != 2) {
                    targetOption.remove();
                }
            });
        }
        // Сдам
        else if (advType == 1) {
            options.each(function (index, value) {
                var targetOption = $(value);
                if (targetOption.val() == 2 || targetOption.val() == 4) {
                    targetOption.remove();
                }
            });
        }

        $('#advertisementsliderform-label').val(1).selectpicker('refresh');
    });

});