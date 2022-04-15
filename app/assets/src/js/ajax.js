$(function () {

    const translations = {
        'error_title': 'Возникла ошибка.',
        'error_description': 'Возникла критическая ошибка. Пожалуйста, обратитесь в службу технической поддержки.',
    };

    // global add CSRF-Token to ajax any request
    $.ajaxPrefilter(function (options, originalOptions, xhr) {
        if (!options.crossDomain) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        }
    });

    // AJAX redirect handler
    $(document).ajaxComplete(function (event, xhr) {
        let url = xhr.getResponseHeader('X-Redirect');
        try {
            let response = $.parseJSON(xhr.responseText);
            if (response.hasOwnProperty('data') && response.data.hasOwnProperty('redirect')) {
                url = response.data.redirect;
            }
        } catch (e) {
            //
        }
        if (url) {
            window.location = url;
        }
    });

    // processing errors from server
    window.ajaxProcessingError = function (response, $form) {
        if (response.getResponseHeader('Redirected-To') || response.getResponseHeader('X-Redirect')) {
            return;
        }
        if (response.status === 422) {
            // Ошибка валидации

            const getInputByName = (name) => $form.find("[name='" + fieldName + "']");
            const getNameByKey = (key) => `${key.split('-')[0]}[${key.split('-')[1]}]`;

            let key = Object.keys(response.responseJSON)[0];
            let fieldName = getNameByKey(key);
            let $element = getInputByName(fieldName);

            if (!$element.length) {
                fieldName += '[]'; // Если не найден контрол для валидации, возможно это массив
                $element = getInputByName(fieldName);

                if ($element) {
                    delete Object.assign(response.responseJSON, {[fieldName]: response.responseJSON[key] })[key];
                }
            }

            //$form.displayValidation(response.responseJSON.errors);
            //$form.displayValidation(response.responseJSON);
            $form.displayValidation('init', response.responseJSON);

            // Скролим до первого элемента с ошибкой
            let doScroll = false;
            $.each(response.responseJSON, (key) => {
                if (doScroll) return;

                let fieldName = key.indexOf('-') !== -1 ? getNameByKey(key) : key;
                let $element = getInputByName(fieldName);

                // Скролим до полей, передающих массив
                !$element.length && ($element = getInputByName(fieldName + '[]'));

                if ($element.length) {
                    $('html, body').animate({
                        scrollTop: $element.offset().top - 48
                    }, 1500);
                    doScroll = true;
                }
            });
        } else {
            if (typeof response.responseJSON == 'undefined') {
                window.openWindow(translations.error_title, translations.error_description);
            } else {
                window.openWindow(translations.error_title, response.responseJSON.message);
            }
        }
    };


    // send ajax request
    //
    // example with form:
    // --------------------------------------------
    // window.ajaxRequest($form, function ($card) {
    //     return {
    //         beforeSend: function () {},
    //         complete: function () {},
    //         success: function (response) {}
    //     }
    // });
    // --------------------------------------------
    //
    // example with object:
    // --------------------------------------------
    // window.ajaxRequest({
    //         type: 'POST',
    //         url: $button.data('url'),
    // }, function ($card) {
    //     return {
    //         beforeSend: function () {},
    //         complete: function () {},
    //         success: function (response) {}
    //     }
    // });
    // --------------------------------------------
    //
    window.ajaxRequest = function (object, params) {

        params = params || {};

        let defaultParams = {
            dataType: 'json',
            type: 'GET',
        };

        let $form = null;

        if ($(object).is('form')) {
            defaultParams = $.extend(defaultParams, {
                type: object.attr('method'),
                url: object.attr('action'),
                data: new FormData(object[0]),
                processData: false,
                contentType: false,
            });
            $form = $(object);
        } else {
            defaultParams = $.extend(defaultParams, object);
        }

        defaultParams = $.extend(defaultParams, {
            beforeSend: function () {

                $form && formBeforeSend($form);
                params.beforeSend && params.beforeSend();
            },
            complete: function (response) {

                $form && formComplete($form);
                params.complete && params.complete(response);
            },
            success: function (response) {
                if (response.status === 'success' && response.message) {
                    window.toastSuccess(response.message);
                }
                if (params.success) {
                    checkResponse(response, params.success);
                }
            },
            error: function (response) {
                window.ajaxProcessingError(response, $form);

                let commonErrors = getCommonErrors(response);

                if (commonErrors) {
                    console.log('commonErrors');
                    window.openWindow(translations.error_title, commonErrors[0]);
                    // window.openWindow(translations.error_title, commonErrors[Object.keys(commonErrors)[0]]);
                }
            }
        });

        $.ajax(defaultParams);
    };

    function formBeforeSend($form) {

        $form.prop('disabled', true);
        $form.find('button').attr('disabled', 'disabled');
        $form.find('button span').css('display', 'inherit');
    }

    function formComplete($form) {

        $form.prop('disabled', false);
        $form.find('button').removeAttr('disabled');
        $form.find('button span').css('display', 'none');
    }

    function getCommonErrors(response) {
        try {
            // console.log(response.responseJSON);
            return response.responseJSON.errors['']; // при валидации тут пусто, поэтому не выводим модальное окно
            // return response.responseJSON;
        } catch (e) {
            return null;
        }
    }

    /**
     * Проверка на корректность ответа
     * @param response
     * @param callback
     */
    function checkResponse(response, callback) {
        if (response.hasOwnProperty('data') === false) {
            window.openWindow(translations.error_title, translations.error_description);
            return;
        }
        try {
            callback(response);
        } catch (e) {
            console.log('checkResponse exception');
            console.log(e);
            window.openWindow(translations.error_title, translations.error_description);
        }
    }
});
