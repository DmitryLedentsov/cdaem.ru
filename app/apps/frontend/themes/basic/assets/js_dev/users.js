jQuery(function () {

    /**
     * Отправка формы "Профиль"
     */
    $('#form-profile').formApi({

        // Все поля
        fields: [
            "_csrf",
            "Profile[name]",
            "Profile[surname]",
            "Profile[second_name]",
            "Profile[about_me]",
            "Profile[user_type]",
            "Profile[phone2]",
            "Profile[email]",
            "Profile[skype]",
            "Profile[ok]",
            "Profile[vk]",
            "Profile[fb]",
            "Profile[twitter]",
            "Profile[legal_person]",
            "Profile[image]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "profile-name",
            "profile-surname",
            "profile-second_name",
            "profile-about_me",
            "profile-user_type",
            "profile-phone2",
            "profile-email",
            "profile-skype",
            "profile-ok",
            "profile-vk",
            "profile-fb",
            "profile-twitter",
            "profile-legal_person",
            "profile-image"
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
        }
    });


    /**
     * Отправка формы "Изменить пароль"
     */
    $('#form-password').formApi({

        // Все поля
        fields: [
            "_csrf",
            "PasswordForm[oldpassword]",
            "PasswordForm[password]",
            "PasswordForm[repassword]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "passwordform-oldpassword",
            "passwordform-password",
            "passwordform-repassword"
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
        }
    });


    /**
     * Отправка формы "Регистрация"
     */
    $('#form-signup').formApi({

        // Все поля
        fields: [
            "_csrf",
            "User[email]",
            "User[phone]",
            "User[password]",
            "User[repassword]",
            "User[agreement]",
            "Profile[user_type]",
            "Profile[name]",
            "Profile[surname]",
            "Profile[second_name]",
            "Profile[whau]",
            "Profile[advertising]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "user-email",
            "user-phone",
            "user-password",
            "user-repassword",
            "user-agreement",
            "profile-user_type",
            "profile-name",
            "profile-surname",
            "profile-second_name",
            "profile-whau",
            "profile-advertising"
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
        }
    });


    /**
     * Отправка формы "Вход"
     */
    $('#form-signin').formApi({

        // Все поля
        fields: [
            "_csrf",
            "LoginForm[username]",
            "LoginForm[password]",
            "LoginForm[rememberMe]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "loginform-username",
            "loginform-password",
            "loginform-rememberme"
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
        }
    });


    /**
     * Отправка формы "Код активации"
     */
    $('#form-resend').formApi({

        // Все поля
        fields: [
            "_csrf",
            "ResendForm[email]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "resendform-email"
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
        }
    });


    /**
     * Отправка формы "Восстановить пароль"
     */
    $('#form-recovery').formApi({

        // Все поля
        fields: [
            "_csrf",
            "RecoveryForm[email]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "recoveryform-email"
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
        }
    });


    /**
     * Отправка формы "Новый пароль"
     */
    $('#form-recovery-confirmation').formApi({

        // Все поля
        fields: [
            "_csrf",
            "RecoveryConfirmationForm[password]",
            "RecoveryConfirmationForm[repassword]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "recoveryconfirmationform-password",
            "recoveryconfirmationform-repassword"
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
        }
    });


    /**
     * Отправка формы "Юридическое лицо"
     */
    $('#form-legal-person').formApi({

        // Все поля
        fields: [
            "_csrf",
            "LegalPerson[name]",
            "LegalPerson[formattedRegisterDate]",
            "LegalPerson[INN]",
            "LegalPerson[PPC]",
            "LegalPerson[account]",
            "LegalPerson[bank]",
            "LegalPerson[KS]",
            "LegalPerson[BIK]",
            "LegalPerson[BIN]",
            "LegalPerson[legal_address]",
            "LegalPerson[physical_address]",
            "LegalPerson[director]",
            "LegalPerson[description]"
        ],

        // Дополнительные поля, будут отправлены по кнопке submit
        extraSubmitFields: {
            submit: "submit"
        },

        // Валидация полей
        validateFields: [
            "legalperson-name",
            "legalperson-formattedRegisterDate",
            "legalperson-inn",
            "legalperson-ppc",
            "legalperson-account",
            "legalperson-bank",
            "legalperson-ks",
            "legalperson-bik",
            "legalperson-bin",
            "legalperson-legal_address",
            "legalperson-physical_address",
            "legalperson-director",
            "legalperson-description"
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
        }
    });


    /**
     * Добавить изображение
     */
    $("#profile-image").bind("change", function (event) {
        if (window.FileReader) {
            var formApi = $('#form-profile').data('formApi');
            formApi.clearFiles();
            $("#image-preview").html('');
            var files = document.getElementById("profile-image").files;
            var file = {};
            file['Profile[image]'] = files[0];
            formApi.addFile(file);
            (function (i) {
                var reader = new FileReader();
                reader.onloadend = function (e) {
                    $("#image-preview").append("<div data-id='" + 'Profile[image]' + "''><img src='" + reader.result + "' alt='' /></div>")
                };
                reader.readAsDataURL(files[i]);
            })(0);
        }
    });


    /**
     * Удалить изображение
     */
    $("#image-preview").bind("click", function (event) {
        event.preventDefault();
        $("#profile-image").val('');
        var target = event.target;
        var formApi = $('#form-profile').data('formApi');

        if (target.tagName == "IMG") {
            var $img = $(target);
            if ($img.data('type') == 'default') {
                return false;
            }
            formApi.removeFile($img.parent().data('id'));
            $img.parent().remove();
        }
    });

});


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