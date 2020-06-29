$(function () {

    var messageError = 'Возникла критическая ошибка, пожалуйста обратитесь в техническую поддержку.';

    // $('#modal-fast-payment').modal('show');

    // Устанавливаем по умолчанию данные для оплаты с карт
    $(".payment-service select[name=system] option[value='BANKOCEAN2R']").prop('selected', true);
    $('.payments-method-logo[data-system="BANKOCEAN2R"]').addClass('active');


    /**
     * Выбор способа оплаты
     * Внутренний счет или платежная система
     */
    $(document).on('click', '.pay-account, .pay-system', function (event) {
        event.preventDefault();
        var $this = $(this);
        var isActive = $this.hasClass('active');
        var type = $this.data('type');

        $('.pay-button').hide();
        $('.payment-way').hide();
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


    /**
     * Выбор способа оплаты из самых популярных
     */
    $(document).on('click', '.payments-method-logo', function (event) {
        event.preventDefault();
        var $this = $(this);

        $('.payments-method-logo').removeClass('active');
        $this.addClass('active');

        $(".payment-service select[name=system] option[value='" + $this.data('system') + "']").prop('selected', true);
        $this.parents('.payment-service').find('input[name="system"]').val($this.data('system'));
    });


    /**
     * Связь списка и основных значков
     */
    $(".payment-service select[name=system]").change(function () {
        var $this = $(this);
        $('.payments-method-logo').removeClass('active');
        $('.payments-method-logo[data-system="' + $this.val() + '"]').addClass('active');
    });


    /**
     * Отправить платежную форму
     */
    $(document).on('submit', '.payment-form', function (event) {
        event.preventDefault();
        var $this = $(this);
        var data = $this.serializeArray();
        $.post($this.attr('action'), data, function (response) {
            if (jQuery.isPlainObject(response) && ('status' in response) == true) {
                if (response.status == 1) {

                    showStackInfo('Информация', response.message);

                    if ($('#modal-realty-objects-by-service').length) {
                        $('#modal-realty-objects-by-service').modal('hide');
                    }

                    if ($('#modal-payment-widget').length) {
                        $('#modal-payment-widget').modal('hide');
                    }

                    if (response.funds) {
                        $('#funds-main').html(response.funds);
                    }

                    if (response.redirect) {
                        if (response.redirect == 'reload') {
                            // Иногда сервис не успевает активироваться сразу
                            setTimeout(function () {
                                document.location.reload();
                            }, 1500);
                        } else {
                            window.location.href = response.redirect;
                        }
                    }

                } else {
                    showStackError('Ошибка', response.message);
                }
            } else {
                showStackError('Ошибка', messageError);
            }
        });
    });

});