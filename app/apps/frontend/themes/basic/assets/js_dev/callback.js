jQuery(function () {

    /**
     * Открыть окно
     */
    jQuery('.open-callback-modal').on('click', function (event) {

        event.preventDefault();
        var $this = $(this);
        var formApi = $('#form-callback').data("formApi");

        if (formApi) {
            formApi.reset();
        }
    });


    /**
     * Отправка формы "Заявки на обратный звонок"
     */
    $('#form-callback').formApi({

        // Все поля
        fields: [
            "_csrf",
            "Callback[phone]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "callback-phone"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $('#modal-callback').modal('hide');
                    showStackInfo("Информация", response.message);
                } else {
                    showStackError('Ошибка', 'Возникла критическая ошибка');
                }
            }

        }
    });


    /**
     * Маска для телефона
     */
    $("#callback-phone").mask("+7 (999) 999-9999");
});