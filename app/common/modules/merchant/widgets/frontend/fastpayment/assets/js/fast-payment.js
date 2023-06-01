var setFormDefaults;

if (typeof $fast_payment_loaded !== 'undefined') {
    // console.log('not load app/common/modules/merchant/widgets/frontend/fastpayment/assets/js/fast-payment.js:9');
    setFormDefaults();
}
else {
    // console.log('load  app/common/modules/merchant/widgets/frontend/fastpayment/assets/js/fast-payment.js:9');
    $fast_payment_loaded = true;

    setFormDefaults = function () {
        $(".payment-service select[name*=system] option[value='BankCardPSR']").prop('selected', true);
        // $('.payments-method-logo[data-system="BANKOCEAN2R"]').addClass('active');

        $('.modal-pay-logo label.visa input')[0].checked = true;

    };

    $(function () {

        var messageError = 'Возникла критическая ошибка, пожалуйста обратитесь в техническую поддержку.';

        // $('#modal-fast-payment').modal('show');

        // Устанавливаем по умолчанию данные для оплаты с карт
        setFormDefaults();

        /**
         * Выбор способа оплаты
         * Внутренний счет или платежная система
         */
        /*
        $(document).on('click', '.pay-account, .pay-system', function (event) {
            console.log("on('click', '.pay-account, .pay-system'");

            event.preventDefault();
            var $this = $(this);
            var isActive = $this.hasClass('active');
            var type = $this.data('type');

            // $('.pay-button').hide();
            // $('.payment-way').hide();
            $('.pay-account, .pay-system').removeClass('active');

            $this.addClass('active');

            $this.parents('.payment-service').find('input[name="type"]').val(type);

            if (isActive) {
                $this.removeClass('active');
            } else {
                $('[data-target="' + type + '"]').slideDown();
                $('.pay-button').slideDown();
            }

            $this.parents('.payment-service').find('input[name="system"]').val('');
        });
        */

        /**
         * Выбор способа оплаты из самых популярных
         */
        // $(document).on('click', '.payments-method-logo', function (event) {
        $(document).on('click', '.logo-pay', function (event) {
            // event.preventDefault(); // не переключаются радиобатоны todo может вернуть индикацию классами?
            var $this = $(this);

            console.log($this);

            $('.payments-method-logo').removeClass('active');
            $this.addClass('active');

            $(".payment-service select[name*=system] option[value='" + $this.data('system') + "']").prop('selected', true);
            $this.parents('.payment-service').find('input[name*="system"]').val($this.data('system'));
        });


        /**
         * Связь списка и основных значков
         */
        $(".payment-service select[name=system]").change(function () {
            var $this = $(this);
            console.log('select on change', $('.logo-pay[data-system="' + $this.val() + '"]'));
            // $('.payments-method-logo').removeClass('active');
            $('.logo-pay').removeClass('active');
            // $('.payments-method-logo[data-system="' + $this.val() + '"]').addClass('active');
            $('.logo-pay[data-system="' + $this.val() + '"]').prop('checked', true);
        });


        /**
         * Отправить платежную форму
         */
        $(document).on('submit', '.payment-form', function (event) {
            console.log("on('submit', '.payment-form'");
            event.preventDefault();
            var $this = $(this);
            var data = $this.serializeArray();
            $.post($this.attr('action'), data, function (response) {
                if (jQuery.isPlainObject(response) && ('status' in response) === true) {
                    if (response.status === 1) {
                        // showStackInfo('Информация', response.message);
                        window.toastSuccess(response.message, 'Информация');
                        window.closeWindow();

                        if (response.funds) {
                            $('#funds-main').html(response.funds);
                        }

                        console.log('Оплата прошла, обновляем токен');
                        updateToken();
                        $responseCache = '';

                        if (response.redirect) {
                            // todo вернуть
                            /*if (response.redirect === 'reload') {
                                // Иногда сервис не успевает активироваться сразу
                                setTimeout(function () {
                                    document.location.reload();
                                }, 1500);
                            } else {
                                window.location.href = response.redirect;
                            }*/
                        }

                    } else {
                        // showStackError('Ошибка', response.message);
                        window.toastError(response.message, 'Ошибка');
                    }
                } else {
                    // showStackError('Ошибка', messageError);
                    window.toastError(messageError, 'Ошибка');
                }
            });
        });

    });

}
