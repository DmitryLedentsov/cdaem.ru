jQuery(function () {

    /**
     * Показать/скрыть объявления
     */
    $('#advertform-rent_type').find('input[type=checkbox]').on('click', function () {
        var $this = $(this);
        if ($this.prop('checked')) {


            var formApi = $('#form-apartment').data('formApi');

            // Сброс данных в скрытых полях
            formApi.reset('advertform-type-' + $this.val());
            formApi.reset('#advertform-price-' + $this.val());
            formApi.reset('#advertform-currency-' + $this.val());
            formApi.targetForm.find('\[name ^= "AdvertForm[currency]"\]').val(1).selectpicker('refresh');

            $('#rent-type-' + $this.val()).addClass('show');
        } else {
            $('#rent-type-' + $this.val()).removeClass('show');
        }
    });


    /**
     * Отправка формы "Быстро снять / Зарезервировать"
     */
    $('#form-reservation').formApi({

        // Все поля
        fields: [
            "_csrf",
            "ReservationForm[city_id]",
            "ReservationForm[address]",
            "ReservationForm[pets]",
            "ReservationForm[children]",
            "ReservationForm[clients_count]",
            "ReservationForm[arrived_date]",
            "ReservationForm[arrived_time]",
            "ReservationForm[out_date]",
            "ReservationForm[out_time]",
            "ReservationForm[actuality_duration]",
            "ReservationForm[rent_type]",
            "ReservationForm[rooms]",
            "ReservationForm[floor]",
            "ReservationForm[beds]",
            "ReservationForm[metro_walk]",
            "ReservationForm[more_info]",
            "ReservationForm[verifyCode]",
            "ReservationForm[money_from]",
            "ReservationForm[money_to]",
            "ReservationForm[currency]",

            "ReservationForm[email]",
            "ReservationForm[phone]",
            "ReservationForm[password]",
            "ReservationForm[agreement]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "reservationform-city_id",
            "reservationform-address",
            "reservationform-pets",
            "reservationform-children",
            "reservationform-clients_count",
            "reservationform-arrived_date",
            "reservationform-arrived_time",
            "reservationform-out_date",
            "reservationform-out_time",
            "reservationform-actuality_duration",
            "reservationform-rent_type",
            "reservationform-rooms",
            "reservationform-floor",
            "reservationform-beds",
            "reservationform-metro_walk",
            "reservationform-more_info",
            "reservationform-verifycode",
            "reservationform-budget",

            "reservationform-email",
            "reservationform-phone",
            "reservationform-password",
            "reservationform-agreement",
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {
            if ($.isPlainObject(response) && ('status' in response) == false) {
                scrollToFirstError(response, formApi.validateFields);
            }
        },

        // Событие срабатывает после завершения ajax запроса
        complete: function (formApi, jqXHR, textStatus) {
            // Обновить защитный код
            if (jqXHR.status != 302) {
                if ($('#reservationform-verifycode-image').length) {
                    $('#reservationform-verifycode-image').yiiCaptcha('refresh');
                }
            }
        }
    });


    /**
     * Отправка формы "Жалоба на владельца"
     */
    $('#form-apartment').formApi({

        // Все поля
        fields: [
            "_csrf",
            "AdvertForm[rent_type][]",
            "AdvertForm[price]",
            "AdvertForm[currency]",
            "ApartmentForm[city]",
            "ApartmentForm[visible]",
            "ApartmentForm[city_id]",
            "ApartmentForm[address]",
            "ApartmentForm[metro_walk]",
            "ApartmentForm[apartment]",
            "ApartmentForm[floor]",
            "ApartmentForm[total_area]",
            "ApartmentForm[total_rooms]",
            "ApartmentForm[beds]",
            "ApartmentForm[remont]",
            "ApartmentForm[description]",
            "ApartmentForm[metro_array][]",
            "ImageForm[files][]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "advertform-rent_type",
            "advertform-type-*",
            "apartmentform-city_id",
            "apartmentform-address",
            "apartmentform-visible",
            "apartmentform-metro_walk",
            "apartmentform-apartment",
            "apartmentform-floor",
            "apartmentform-total_area",
            "apartmentform-total_rooms",
            "apartmentform-beds",
            "apartmentform-remont",
            "apartmentform-description",
            "apartmentform-metro_array",
            "imageform-files"
        ],

        // Событие срабатывает при успешном запросе
        success: function (formApi, response) {

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    showStackInfo('Информация', response.message);
                } else {
                    showStackError('Ошибка', response.message);
                }
            }

            if ($.isPlainObject(response) && ('status' in response) == false) {
                scrollToFirstError(response, formApi.validateFields);
            }
        }
    });


    /**
     * Добавить изображения
     */
    $("#imageform-files").bind("change", function (event) {
        if (window.FileReader) {
            var formApi = $('#form-apartment').data('formApi');
            var files = document.getElementById("imageform-files").files;

            if ($("#images-preview .advert-preview").length >= 10) {
                showStackError('Внимание', 'Вы можете загрузить до 10 изображений Вашего объекта. Сейчас загружен максимум.');

            } else {

                var filesLength = files.length;

                if (files.length > 10 || (($("#images-preview .advert-preview").length + files.length) > 10)) {

                    var thisLength = $("#images-preview .advert-preview").length;

                    filesLength = (thisLength > 0) ? (10 - thisLength) : 10;

                    showStackError('Внимание', 'Вы можете загрузить до 10 изображений Вашего объекта. Остальные изображения будут проигнорированы.');

                }

// 				else {
                for (var i = 0, m = filesLength; i < m; i++) {
                    if (i >= 10) {
                        continue;
                    }
                    var file = {};
                    file['ImageForm[files][' + i + ']'] = files[i];
                    formApi.addFile(file);
                    (function (i) {
                        var reader = new FileReader();
                        reader.onloadend = function (e) {

                            var block =
                                '<div class="advert-preview shadow-box" data-image="ImageForm[files][' + i + ']" style="margin-right: 15px; margin-bottom: 15px;">' +
                                '<div class="control"><div class="delete"></div></div>' +
                                '<div class="apartment-wrap">' +
                                '<div class="image">' +
                                '<img src="' + reader.result + '" alt="" />' +
                                '</div>' +
                                '</div>' +
                                '</div>';

                            $("#images-preview").append(block);
                        };
                        reader.readAsDataURL(files[i]);
                    })(i);
                }
// 	            }
            }
        }
    });


    /**
     * Управление изображениями
     */
    $(document).on("click", ".images-preview", function (event) {
        event.preventDefault();

        var $target = $(event.target);
        var $targetAdvert = $target.parents('.advert-preview');

        // Главное изображение
        if ($target.hasClass('index')) {
            $.getJSON("/office/control-image/index/" + $targetAdvert.data('image'), function (response) {
                if (response.status == 1) {
                    $targetAdvert.find('.control .index').remove();
                    $targetAdvert.addClass('default');
                    var otherAdvert = $('.images-preview .advert-preview').not($targetAdvert);
                    otherAdvert.removeClass('default');
                    otherAdvert.find('.index').remove();
                    otherAdvert.find('.control').prepend('<div class="index" title="Сделать главным"></div>');
                    showStackInfo('Информация', 'Данные сохранены успешно');
                } else {
                    showStackError('Ошибка', 'Возникла критическая ошибка');
                }
            });
        }

        // Удалить изображение
        else if ($target.hasClass('delete')) {
            if ($targetAdvert.hasClass('loaded')) {
                $.getJSON("/office/control-image/delete/" + $targetAdvert.data('image'), function (response) {
                    if (response.status == 1) {
                        $targetAdvert.remove();
                        showStackInfo('Информация', 'Данные сохранены успешно');
                    } else {
                        showStackError('Ошибка', 'Возникла критическая ошибка');
                    }
                });
            } else {
                var formApi = $('#form-apartment').data('formApi');
                formApi.removeFile($targetAdvert.data('image'));
                $targetAdvert.remove();
            }
        }
    });


    /**
     * Отказать или подтвердить заявку на бронь
     */
    $(document).on("click", ".reservation-send", function (event) {
        event.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-confirm",
            method: 'POST',
            data: {
                action: 'confirm',
                type: $this.data('type'),
                reservation_id: $this.data('reservation'),
                department: $this.data('department'),
                user_type: $this.data('user-type'),
                user_id: $this.data('user-id')
            },
            context: document.body
        }).done(function (response) {
            $('#modal-reservation-confirm').remove();
            $('body').append(response);
            $('#modal-reservation-confirm').modal('show');
        })
    });


// Открытие блока с описанием для мобильных телефонов
    $("#opendescription").click(function () {
        $("#information-advert").slideToggle("slow", function () {

        });
    });


    /**
     * Форма проверка на заполнение подобрать квартиру
     */


    /**
     * Уточнить выбор отмены или подтверждения заявки на бронь
     */
    $(document).on("click", "#button-reservation-confirm", function (event) {
        event.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-confirm",
            method: 'POST',
            data: {
                action: 'send',
                type: $this.data('type'),
                reservation_id: $this.data('reservation'),
                cancel_reason: $('#reservation-confirm-reason').val(),
                department: $this.data('department'),
                user_type: $this.data('user-type'),
                priced: $this.data('priced'),
                user_id: $this.data('user-id')
            },
            context: document.body
        }).done(function (response) {
            var formGroup = $('#reservation-confirm-reason').parents('.form-group');
            formGroup.removeClass('has-success');
            formGroup.removeClass('has-error');
            formGroup.find('.help-block').html('');

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $('#modal-reservation-confirm').find('.modal-body').html('<div class="alert alert-info">' + response.message + '</div>');
                    setTimeout(function () {
                        document.location.reload();
                    }, 4000);
                } else {
                    showStackError('Ошибка', response.message);
                }
            } else if ($.isArray(response)) {
                if (response[0]) {
                    formGroup.addClass('has-error');
                    formGroup.find('.help-block').html(response[0]);
                }
            }
        })
    });


    /**
     * Незаезд
     */
    $(document).on("click", "#button-reservation-failure", function (event) {
        event.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-failure/" + $this.data('id'),
            method: 'GET',
            context: document.body
        }).done(function (response) {
            $('#modal-reservation-failure').remove();
            $('body').append(response);
            $('#modal-reservation-failure').modal('show');
        })
    });


    /**
     * Подтверждение незаезда
     */
    $(document).on("click", "#button-reservation-failure-confirm", function (event) {
        event.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-failure/" + $this.data('id'),
            method: 'POST',
            data: {
                cause_text: $('#reservation-failure-reason').val(),
            },
            context: document.body
        }).done(function (response) {
            var formGroup = $('#reservation-failure-reason').parents('.form-group');
            formGroup.removeClass('has-success');
            formGroup.removeClass('has-error');
            formGroup.find('.help-block').html('');

            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $('#modal-reservation-failure').find('.modal-body').html('<div class="alert alert-info">' + response.message + '</div>');
                } else {
                    showStackError('Ошибка', response.message);
                }
            } else if ($.isArray(response)) {
                if (response[0]) {
                    formGroup.addClass('has-error');
                    formGroup.find('.help-block').html(response[0]);
                }
            }
        })
    });

});


var $preloadFlag = false; // Флаг загрузки, чтобы избежать повторных запросов

/**
 * Написать сообщение владельцу
 * @param userId
 */
function writeMessage(userId) {

    if (!$preloadFlag) {
        $preloadFlag = true;

        var jqxhr = $.get("/ajax/message/" + userId, function (response) {
            $('#modal-message').remove();
            $('body').append(response);
            $('#modal-message').modal('show');
        });

        jqxhr.complete(function () {
            $preloadFlag = false;
        });
    }
}

$(document).ready(function () {
    $("#country").on('change', '', function (e) {
        if ($("#country option:selected").text() == "Russia") {
            $("#phone").inputmask("+79999999999");
        }
        if ($("#country option:selected").text() == "Ukraine") {
            $("#phone").inputmask("+380999999999");
        }
        if ($("#country option:selected").text() == "Belarus") {
            $("#phone").inputmask("+375999999999");
        }
    });
});



