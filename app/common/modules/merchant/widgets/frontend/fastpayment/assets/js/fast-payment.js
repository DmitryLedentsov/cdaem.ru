let setFormDefaults;
if (typeof $isFastPaymentLoaded !== 'undefined') {
    setFormDefaults();
}
else {
    $isFastPaymentLoaded = true;
    setFormDefaults = function () {
        $(".payment-service select[name*=system] option[value='BankCardPSR']").prop('selected', true);
        $('.modal-pay-logo label.visa input')[0].checked = true;
    };

    $(function () {
        var messageError = 'Возникла критическая ошибка, пожалуйста обратитесь в техническую поддержку.';
        // Устанавливаем по умолчанию данные для оплаты с карт
        setFormDefaults();

        /**
         * Выбор способа оплаты из самых популярных
         */
        $(document).on('click', '.logo-pay', function (event) {
            var $this = $(this);
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
            $('.logo-pay').removeClass('active');
            $('.logo-pay[data-system="' + $this.val() + '"]').prop('checked', true);
        });

        /**
         * Отправить платёжную форму, старый вариант
         */
        $(document).on('submit', '#payment-form', function (event) {
            console.log("on('submit', '#payment-form'");
            event.preventDefault();
            var $this = $(this);
            var data = $this.serializeArray();
            $.post($this.attr('action'), data, function (response) {
                response = JSON.parse(response);
                if (jQuery.isPlainObject(response) && ('status' in response) === true) {
                    if (response.status === 'success') {
                        window.toastSuccess(response.message, 'Информация');
                        window.closeWindow();
                        if (response.funds) {
                            $('#funds-main').html(response.funds);
                        }
                        console.log('Оплата прошла, обновляем токен');
                        updateToken();
                        $responseCache = '';
                        if (response.redirect) {
                            if (response.redirect === 'reload') {
                                // Иногда сервис не успевает активироваться сразу
                                setTimeout(function () {
                                    document.location.reload();
                                }, 1500);
                            } else {
                                window.location.href = response.redirect;
                            }
                        }
                    } else {
                        window.toastError(response.message, 'Ошибка');
                    }
                } else {
                    window.toastError(messageError, 'Ошибка');
                }
            });
        });

        /**
         * Отправить платежную форму, новый вариант
         */
        /*$(document).on('submit', '#payment-form', function (event) {
            console.log('submit #payment-form');
            event.preventDefault();
            var $this = $(this);
            window.ajaxRequest($this,{
                success: data => {
                    if (data.hasOwnProperty('status') && data.status === 'success') {
                        $('#main-modal').modal('hide');
                        updateToken();
                    }
                },
                commonError: message => {
                    $('.payment-form > .section:last-child').after(
                        $('<div>', {class: 'alert alert-danger', role: 'alert'}).append(message)
                    );
                }
            });
        });*/
    });
}
