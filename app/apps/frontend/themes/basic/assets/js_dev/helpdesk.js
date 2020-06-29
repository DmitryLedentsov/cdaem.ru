jQuery(function () {

    /**
     * Оставить жалобу
     */
    $(document).on("click", '#helpdesk-complaint', function () {
        var $this = $(this);
        $.get('/complaint/' + $this.data('id'), function (response) {
            $('#modal-helpdesk-complaint').remove();
            $('body').append(response);
            $('#modal-helpdesk-complaint').modal('show');
        });
        return false;
    });


    /**
     * Отправка формы "Задать вопрос"
     */
    $('#form-helpdesk-ask').formApi({

        // Все поля
        fields: [
            "_csrf",
            "Helpdesk[user_name]",
            "Helpdesk[email]",
            "Helpdesk[priority]",
            "Helpdesk[theme]",
            "Helpdesk[text]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "helpdesk-user_name",
            "helpdesk-email",
            "helpdesk-priority",
            "helpdesk-theme",
            "helpdesk-text"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

        }
    });

});


$(document).ready(function () {
    $("#country").on('change','',function(e){
        if ($("#country option:selected").text() == "Russia") {
        $("#phone").inputmask("+79999999999");}
        if ($("#country option:selected").text() == "Ukraine") {
        $("#phone").inputmask("+380999999999"); }
    if ($("#country option:selected").text() == "Belarus") {
        $("#phone").inputmask("+375999999999"); } 
});
});