jQuery(function () {

    /**
     * Слайдер
     */
    var $targetGallery = $("#gallery");

    if ($targetGallery.length) {
        // Слайдер
        $targetGallery.owlCarousel({
            autoWidth: true,
            loop: true,
            margin: 10,
            nav: false,
            autoplay: true,
            autoplayTimeout: 3000,
            smartSpeed: 1000,
            autoplayHoverPause: false,
            navText: [
                "",
                ''
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        });
    }


    /**
     * Подробная информация о методах оплаты
     */
    $('.payment-methods .pm-icon').on('click', function (event) {
        var $this = $(this);

        if (!$this.parent().hasClass('no-active') && $(event.target).hasClass('pm-icon')) {

            $.each($('.payment-methods form'), function () {
                $(this).data('formApi').reset();
            });

            $('.payment-methods .pm-icon').find('.payment-info').not($this.find('.payment-info')).hide();
            $this.find('.payment-info').toggle();
        }
    });


    /**
     * Маска для телефона
     */
    $(".phone-mask").mask("+7 (999) 999-9999");


    /**
     * Отправка формы "Заявки на отправку реквизитов"
     */
    $('.payment-methods form').formApi({

        // Все поля
        fields: [
            "_csrf",
            "DetailsHistoryForm[advert_id]",
            "DetailsHistoryForm[type]",
            "DetailsHistoryForm[payment]",
            "DetailsHistoryForm[phone]",
            "DetailsHistoryForm[email]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "detailshistoryform-type",
            "detailshistoryform-payment",
            "detailshistoryform-phone",
            "detailshistoryform-email"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    formApi.targetForm.parent().html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    showStackError('Ошибка', response.message);
                }
            }
        }
    });


    /**
     * Отправка формы "Забронировать"
     */
    $('#form-reserved').formApi({

        // Все поля
        fields: [
            "_csrf",
            "Reservation[name]",
            "Reservation[email]",
            "Reservation[phone]",
            "Reservation[arrived_date]",
            "Reservation[arrived_time]",
            "Reservation[out_date]",
            "Reservation[out_time]",
            "Reservation[transfer]",
            "Reservation[clients_count]",
            "Reservation[more_info]",
            "Reservation[whau]",
            "Reservation[verifyCode]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "reservation-name",
            "reservation-email",
            "reservation-phone",
            "reservation-arrived_date",
            "reservation-arrived_time",
            "reservation-out_date",
            "reservation-out_time",
            "reservation-transfer",
            "reservation-clients_count",
            "reservation-more_info",
            "reservation-whau",
            "reservation-verifycode"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $('#reserved-result').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#reserved-result').html('<div class="alert alert-danger">' + response.message + '</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>');
                }
            }

        },

        // Событие срабатывает после завершения ajax запроса
        complete: function (formApi, jqXHR, textStatus) {
            if (jqXHR.status != 308 && jqXHR.status != 302) {
                // Обновить защитный код
                if ($('#reservation-verifycode-image').length) {
                    $('#reservation-verifycode-image').yiiCaptcha('refresh');
                }
            }
        }
    });


    /**
     * Отправка формы "Хочу сдать квартиру"
     */
    $('#form-want-pass').formApi({

        // Все поля
        fields: [
            "_csrf",
            "WantPassForm[rent_types_array][]",
            "WantPassForm[metro_array][]",
            "WantPassForm[address]",
            "WantPassForm[name]",
            "WantPassForm[phone]",
            "WantPassForm[phone2]",
            "WantPassForm[email]",
            "WantPassForm[rooms]",
            "WantPassForm[description]",
            "WantPassForm[files][]",
            "WantPassForm[verifyCode]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "wantpassform-rent_types_array",
            "wantpassform-metro_array",
            "wantpassform-address",
            "wantpassform-name",
            "wantpassform-phone",
            "wantpassform-phone2",
            "wantpassform-email",
            "wantpassform-rooms",
            "wantpassform-description",
            "wantpassform-files",
            "wantpassform-verifycode"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $('#want-pass-result').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#want-pass-result').html('<div class="alert alert-danger">' + response.message + '</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>');
                }
            }

        },

        // Событие срабатывает после завершения ajax запроса
        complete: function (formApi, jqXHR, textStatus) {
            if (jqXHR.status != 302) {
                // Обновить защитный код
                if ($('#wantpassform-verifycode-image').length) {
                    $('#wantpassform-verifycode-image').yiiCaptcha('refresh');
                }
            }
        }
    });


    /**
     * Отправка формы "Быстро подберем квартиру"
     */
    $('#form-select').formApi({

        // Все поля
        fields: [
            "_csrf",
            "SelectForm[rent_types_array][]",
            "SelectForm[metro_array][]",
            "SelectForm[name]",
            "SelectForm[phone]",
            "SelectForm[phone2]",
            "SelectForm[email]",
            "SelectForm[rooms]",
            "SelectForm[description]",
            "SelectForm[verifyCode]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "selectform-rent_types_array",
            "selectform-metro_array",
            "selectform-name",
            "selectform-phone",
            "selectform-phone2",
            "selectform-email",
            "selectform-rooms",
            "selectform-description",
            "selectform-verifycode"
        ],


        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $('#select-result').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#select-result').html('<div class="alert alert-danger">' + response.message + '</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>');
                }
            }

        },

        // Событие срабатывает после завершения ajax запроса
        complete: function (formApi, jqXHR, textStatus) {
            if (jqXHR.status != 302) {
                // Обновить защитный код
                if ($('#selectform-verifycode-image').length) {
                    $('#selectform-verifycode-image').yiiCaptcha('refresh');
                }
                $("html, body").animate({scrollTop: $('div.alert').height()}, 2000);
            }
        }
    });


    /**
     * Добавить изображение
     */
    $("#wantpassform-files").bind("change", function (event) {
        if (window.FileReader) {
            var maxFiles = 60;
            var formApi = $('#form-want-pass').data('formApi');
            var files = document.getElementById("wantpassform-files").files;
            if (files.length > maxFiles || (($("#images-preview img").length + files.length) > maxFiles)) {
                showStackError('Внимание', 'Вы можете загрузить до ' + maxFiles + ' изображений Вашего объекта. Остальные изображения будут проигнорированы.');
            } else {
                for (var i = 0, m = files.length; i < m; i++) {
                    if (i >= maxFiles) {
                        continue;
                    }
                    var file = {};
                    file['WantPassForm[files][' + i + ']'] = files[i];
                    formApi.addFile(file);
                    (function (i) {
                        var reader = new FileReader();
                        reader.onloadend = function (e) {
                            $("#images-preview").append("<div style='width:150px; display: inline-block; margin-right: 5px;' data-id='" + 'WantPassForm[files][' + i + ']' + "''><img style='width: 100%' src='" + reader.result + "' alt='' /></div>")
                        };
                        reader.readAsDataURL(files[i]);
                    })(i);
                }
            }
        }
    });


    /**
     * Удалить изображение
     */
    $("#images-preview").bind("click", function (event) {
        event.preventDefault();
        var target = event.target;
        var formApi = $('#form-want-pass').data('formApi');

        if (target.tagName == "IMG") {
            var $img = $(target);
            formApi.removeFile($img.parent().data('id'));
            $img.parent().remove();
        }
    });
});
