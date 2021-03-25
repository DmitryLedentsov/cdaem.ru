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
    $(document).ajaxComplete(function (event, xhr, settings) {
        let url = null;
        try {
            let response = $.parseJSON(xhr.responseText);
            url = xhr.getResponseHeader('X-Redirect');
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
        if (response.getResponseHeader('Redirected-To')) {
            return;
        }
        if (response.status === 422) {
            $form.displayValidation(response.responseJSON.errors);
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

                if (params.success) {
                    checkResponse(response, params.success);
                }
            },
            error: function (response) {
                window.ajaxProcessingError(response, $form);

                let commonErrors = getCommonErrors(response);

                if (commonErrors) {
                    window.openWindow(translations.error_title, commonErrors[0]);
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
            return response.responseJSON.errors[''];
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
