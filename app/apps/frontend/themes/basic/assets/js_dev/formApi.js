/**
 * FormApi виджет
 * --------------
 *
 * Позволяет отправлять данные на сервер через FormData.
 * Поддерживает простую обработку и валидацию форм.
 *
 * ПРИМЕР ИСПОЛЬЗОВАНИЯ:
 * -------------------------------------------------
 * $('#form-test').formApi({
 *
 *       // Все поля
 *       fields: ["_csrf", "Model[text]", "Model[checkbox][]", "Model[radio]", "Model[select]", "Model[files]"],
 *
 *       // Отключить валидацию
 *       validateCustom: false,
 *
 *       // Дополнительные поля, будут отправлены по кнопке submit
 *       extraSubmitFields: {
 *           submit: "submit"
 *       },
 *
 *       // Валидация полей
 *       validateFields: [
 *           "model-text",
 *           "model-checkbox",
 *           "model-radio",
 *           "model-select",
 *           "model-files"
 *       ],
 *
 *       // Валидация полей, расширенный синтаксис
 *       validateFields: [
 *           {
 *               id: "model-text",
 *               extraParam: true
 *           },
 *           {
 *               id: "model-checkbox",
 *               extraParam: false
 *           },
 *           {
 *               id: "model-radio",
 *               extraParam: true
 *           },
 *           {
 *               id: "model-select",
 *               extraParam: false
 *           },
 *           {
 *               id: "model-files",
 *               extraParam: false
 *           }
 *       ],
 *
 *       // Валидация полей в реальном времени
 *       validateFieldsKeyUp: ["model-test1", "model-test2"],
 *
 *       // Событие перед отправкой формы
 *       beforeSubmit: function (formApi) {
 *           console.log('beforeSubmit #form-test');
 *       },
 *
 *       // Событие срабатывает перед ajax запросом
 *       beforeSend: function (formApi, jqXHR, textStatus) {
 *           console.log('beforeSend #form-test');
 *       },
 *
 *       // Событие срабатывает перед валидацией атрибутов
 *       beforeValidate: function (formApi, jqXHR, textStatus) {
 *           console.log('beforeSend #form-test');
 *       },
 *
 *       // Событие срабатывает после валидации атрибутов
 *       afterValidate: function (formApi, jqXHR, textStatus) {
 *           console.log('beforeSend #form-test');
 *       },
 *
 *       // Событие срабатывает при успешном запросе
 *       success: function (formApi, response) {
 *           console.log('success #form-test');
 *           if ($.isPlainObject(response)) {
 *               if (response.status) {
 *                   console.log(response);
 *               } else {
 *                   console.log(response);
 *               }
 *           }
 *       },
 *
 *       // Событие срабатывает при неудачном запросе
 *       error: function (formApi, response) {
 *           console.log('error #form-test');
 *       },
 *
 *       // Событие срабатывает после завершения ajax запроса
 *       complete: function (formApi, jqXHR, textStatus) {
 *           console.log('complete #form-test');
 *       }
 *   });
 *
 * РАБОТА С API
 * -------------------------------------------------
 * Получить доступ к данным формы:
 *
 * $('#form-test').data('formApi')
 *
 *
 * Работа с файлами:
 *
 * Добавить файл; $('#form-test').data('formApi').addFile({'Model[files][0]' : 'file'});
 * Удалить файл: $('#form-test').data('formApi').removeFile('Model[files][0]');
 * Удалить все файлы: $('#form-test').data('formApi').clearFiles();
 *
 *
 * ПРИМЕР ПРОСМОТРА, ДОБАВЛЕНИЯ И УДАЛЕНИЯ ИЗОБРАЖЕНИЙ
 * ---------------------------------------------------
 *
 *   $("#model-files").bind("change", function (event) {
 *       if (window.FileReader) {
 *           var formApi =  $('#form-test').data('formApi');
 *           formApi.clearFiles();
 *           $("#model-images-preview").html('');
 *           var files = document.getElementById("model-files").files;
 *           for (var i = 0, m = files.length; i < m; i++) {
 *               var file = {};
 *               file['Model[files]['+i+']'] = files[i];
 *               formApi.addFile(file);
 *               (function (i) {
 *                   var reader = new FileReader();
 *                   reader.onloadend = function (e) {
 *                       $("#model-images-preview").append("<div style='width:150px;' data-id='"+'Model[files]['+i+']'+"' title='"+i+"'><img style='width: 100%' src='" + reader.result + "' alt='' /></div>")
 *                   };
 *                   reader.readAsDataURL(files[i]);
 *               })(i);
 *           }
 *       }
 *   });
 *
 *
 *   $("#model-images-preview").bind("click", function (event) {
 *       event.preventDefault();
 *       var target = event.target;
 *       var formApi =  $('#form-test').data('formApi');
 *
 *       if (target.tagName == "IMG") {
 *           var $img = $(target);
 *           formApi.removeFile($img.parent().data('id'));
 *           $img.parent().remove();
 *       }
 *   });
 *
 */
;
(function ($) {
    "use strict";
    $.fn.formApi = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.formApi');
            return false;
        }
    };

    var settingsDefaults = {
        flagrequest: 0,
        timeout_id: undefined,
        keyUpValidateTime: 300,
        keyUpValidateField: null
    };

    var attributeDefaults = {
        id: undefined,
        formGroupTarget: ".form-group",
        helpCssClass: 'help-block',
        errorCssClass: 'has-error',
        successCssClass: 'has-success'
    };

    var methods = {

        /**
         * Инициализация форм
         * @param settings
         * @returns {*}
         */
        init: function (settings) {

            if (window.FileReader && window.FormData) {

                return this.each(function () {

                    var $form = $(this);
                    if ($form.data('formApi')) {
                        return;
                    }

                    var fields = settings.fields ? settings.fields.slice() : [];
                    var validateFields = settings.validateFields ? settings.validateFields.slice() : [];
                    var scenarios = settings.scenarios ? settings.scenarios : {};
                    var scenario = settings.scenario ? settings.scenario : null;
                    var validateFieldsKeyUp = settings.validateFieldsKeyUp ? settings.validateFieldsKeyUp.slice() : [];
                    var extraSubmitFields = settings.extraSubmitFields || [];

                    if ($.isArray(validateFields)) {
                        $.each(validateFields, function (i) {
                            if ($.isPlainObject(validateFields)) {
                                validateFields[i] = $.extend({}, attributeDefaults, this);
                            } else {
                                // console.log (validateFields[i]);
                                validateFields[i] = $.extend({}, attributeDefaults, {id: validateFields[i]});
                            }
                        });
                    } else {
                        validateFields = [];
                    }

                    if (validateFieldsKeyUp) {
                        for (var i = validateFieldsKeyUp.length; i--;) {
                            document.getElementById(validateFieldsKeyUp[i]).onkeyup = methods.validateFieldsKeyUp;
                            document.getElementById(validateFieldsKeyUp[i]).onchange = methods.validateFieldsKeyUp;
                            document.getElementById(validateFieldsKeyUp[i]).oninput = methods.validateFieldsKeyUp;
                        }
                    }

                    // TODO: добавить метод submit для принудительной отправки формы, например по  Ctrl+Enter
                    $form.data('formApi', {
                        targetForm: $form,
                        settings: settings,
                        fields: fields,
                        extraSubmitFields: extraSubmitFields,
                        validateFields: validateFields,
                        scenarios: scenarios,
                        scenario: scenario,
                        files: [],
                        addFile: function (file) {
                            this.files.push(file);
                        },
                        removeFile: function (key) {
                            var files = this.files;
                            files.forEach(function (element, i) {
                                if (element[key]) {
                                    files.splice(i, 1);
                                    return true;
                                }
                            });
                            return false;
                        },
                        clearFiles: function () {
                            this.files = [];
                        },
                        reset: function (field) {
                            if (field && field == 'files') {
                                this.clearFiles();
                            } else {
                                if (!field) {
                                    this.clearFiles();
                                }
                                resetFields(this.targetForm, field);
                            }
                        }
                    });

                    $form.on('submit.formApi', methods.submitForm);
                });
            } else {
                // TODO: Оформить
                console.log('Ваш браузер устарел');
            }
        },

        /**
         * Отложенное событие отправки формы для живой валидации
         * @param event
         */
        validateFieldsKeyUp: function (event) {
            if (settingsDefaults.timeout_id) {
                clearTimeout(settingsDefaults.timeout_id);
            }
            settingsDefaults.keyUpValidateField = this.id;
            settingsDefaults.timeout_id = window.setTimeout(function () {

                settingsDefaults.timeout_id && clearTimeout(settingsDefaults.timeout_id);

                var formData = new FormData();
                var $form = $(event.target).parents('form');
                var formApi = $form.data('formApi');

                methods.getFields(formApi, formData);
                methods.sendForm(formApi, formData);

            }, settingsDefaults.keyUpValidateTime);
        },

        /**
         * Событие при отправки формы
         * @param event
         */
        submitForm: function (event) {

            var formData = new FormData();
            var $form = $(this);
            var formApi = $form.data('formApi');

            if (event) {
                methods.stopPropagation(event);
                methods.deleteEvent(event);
                settingsDefaults.keyUpValidateField = null;

                // Событие перед отправкой формы
                if (formApi.settings.beforeSubmit) {
                    formApi.settings.beforeSubmit(formApi);
                }
            }

            methods.getFields(formApi, formData);

            // Добавляем дополнительные поля, только если форма отправлена по кнопке
            if (event) {
                if (formApi.extraSubmitFields) {
                    for (var name in formApi.extraSubmitFields) {
                        formData.append(name, formApi.extraSubmitFields[name]);
                    }
                }
            }

            methods.sendForm(formApi, formData);
        },

        /**
         * Собрать все поля
         * @param formApi
         * @param formData
         */
        getFields: function (formApi, formData) {

            var $form = formApi.targetForm;

            // Добавляем поля в форму
            //var fieldsName = formApi.fields.reverse();
            var fieldsName = formApi.fields;

            for (var i = fieldsName.length; i--;) {

                var field = $form.find('\[name ^= "' + fieldsName[i] + '"\]');

                if (field.length == 1) {
                    if (field.attr('type') == 'radio' && !field.prop("checked")) {
                        return;
                    }
                    if (field.attr('type') == 'checkbox' && !field.prop("checked")) {
                        return;
                    }
                    formData.append([fieldsName[i]], field.val() || '');
                } else if (field.length > 1) {
                    field.each(function () {
                        if (this.type == 'radio' && !this.checked) {
                            return;
                        }
                        if (this.type == 'checkbox' && !this.checked) {
                            return;
                        }
                        formData.append([this.name], this.value || '');
                    });
                }
            }

            // Добавляем файлы
            if (formApi.files.length) {
                methods.getFiles($form, formData);
            }
        },

        /**
         * Отправить форму на сервер
         * @param formApi
         * @param formData
         */
        sendForm: function (formApi, formData) {

            var $form = formApi.targetForm;

            if (settingsDefaults.flagrequest) {
                return;
            }

            settingsDefaults.flagrequest = 1;

            $form.find("[type='submit']").attr('disabled', "disabled");

            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                complete: function (jqXHR, textStatus) {
                    settingsDefaults.flagrequest = 0;
                    $form.find("[type='submit']").removeAttr('disabled');

                    // AJAX редирект
                    var url = jqXHR.getResponseHeader('X-Redirect');
                    if (url) {
                        window.location = url;
                    }

                    if (formApi.settings.complete) {
                        formApi.settings.complete(formApi, jqXHR, textStatus);
                    }
                },
                beforeSend: function (jqXHR, settings) {
                    if (formApi.settings.beforeSend) {
                        formApi.settings.beforeSend(formApi, jqXHR, settings);
                    }
                },
                success: function (response) {
                    methods.responseSuccess(formApi, response);
                },
                error: function (response) {
                    methods.responseError(formApi, response);
                }
            });
        },

        /**
         * Возникла критическая ошибка
         * @param formApi
         * @param response
         */
        responseError: function (formApi, response) {
            if (response.status && (response.status == 301 || response.status == 302 || response.status == 500)) {
                return;
            }
            if (formApi.settings.error) {
                formApi.settings.error(formApi, response);
            }
        },

        /**
         * Успешный ответ от сервера
         * @param formApi
         * @param response
         */
        responseSuccess: function (formApi, response) {
            if (!$.isPlainObject(response) && !$.isArray(response)) {
                methods.responseError(formApi, response);
                return;
            }
            var $form = formApi.targetForm;
            var nameId;
            var formGroup;
            var fieldsName = settingsDefaults.fields;

            if (formApi.settings.beforeValidate) {
                formApi.settings.beforeValidate(formApi);
            }

            if (!formApi.settings.validateCostum) {
                verifyFields(formApi, response);
            }

            if (formApi.settings.afterValidate) {
                formApi.settings.afterValidate(formApi);
            }

            if (formApi.settings.success) {
                formApi.settings.success(formApi, response);
            }
        },

        /**
         * Выбор файлов
         * @param $form
         * @param formData
         */
        getFiles: function ($form, formData) {

            var formApi = $form.data('formApi');

            // var name = "files";
            //name = "Model[files]";

            /*if (settingsDefaults.filesName) {
             name = settingsDefaults.filesName;
             }*/

            for (var i = 0, m = formApi.files.length; i < m; i++) {
                for (var name in formApi.files[i]) {
                    formData.append(name, formApi.files[i][name]);
                    //formData.append(name + "[" + i + "]", formApi.files[i][key]);
                }
            }
        },

        stopPropagation: function (event) {  // сервисный метод остановка всплытия
            event.stopPropagation ? event.stopPropagation() : event.cancelBubble = true;
        },

        deleteEvent: function (event) { // сервисный метод - отмена действий браузера по умолчанию
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
        }
    };

    /**
     * Сброс данных формы
     * @param $form
     * @param field
     */
    var resetFields = function ($form, field) {

        var formApi = $form.data('formApi');

        var validateResetFields = [];
        var resetFields = [];
        for (var validateField in formApi.validateFields) {
            if (field) {
                if (field == formApi.validateFields[validateField].id) {
                    validateResetFields.push(formApi.validateFields[validateField].id);
                } else if (field.search(formApi.validateFields[validateField].id.substr(-1) == '*')) {
                    if (field.search(formApi.validateFields[validateField].id.substring(0, formApi.validateFields[validateField].id.length - 1)) >= 0) {
                        validateResetFields.push(field);
                    }
                }
            } else {
                validateResetFields.push(formApi.validateFields[validateField].id);
            }
        }

        for (var validateField in validateResetFields) {
            var $targetField = formApi.targetForm.find('#' + validateResetFields[validateField]);
            if ($targetField) {
                resetValue($targetField);

                $targetField.trigger('reset');
                $targetField.parents(formApi.validateFields[validateField].formGroupTarget)
                    .removeClass(formApi.validateFields[validateField].successCssClass)
                    .removeClass(formApi.validateFields[validateField].errorCssClass)
                    .find('.' + formApi.validateFields[validateField].helpCssClass).html('');
            }
        }

        // Очистка полей не вошедших в список validateFields
        $targetField = formApi.targetForm.find(field);
        if ($targetField.length) {
            $targetField.each(function () {
                if (this.type == 'text' || this.type == 'textarea') {
                    this.value = '';
                } else if (this.type == 'radio' || this.type == 'checkbox') {
                    this.checked = false;
                } else if (this.type == 'select-one' || this.type == 'select-multiple') {
                    this.value = 'All';
                }
            });
        }
    };

    /**
     * Сброс данных поля
     * @param $targetField
     */
    var resetValue = function ($targetField) {
        $targetField.each(function () {
            if (this.type == 'text' || this.type == 'textarea') {
                this.value = '';
            } else if (this.type == 'radio' || this.type == 'checkbox') {
                this.checked = false;
            } else if (this.type == 'select-one' || this.type == 'select-multiple') {
                this.value = 'All';
            }
        });
    };

    /**
     * Подсветить поля (Результат валидации)
     * @param formApi
     * @param response
     */
    var verifyFields = function (formApi, response) {

        var validateFields = formApi.validateFields;

        var scenarios = [];
        if (formApi.scenario && formApi.scenarios[formApi.scenario]) {
            scenarios = formApi.scenarios[formApi.scenario];
        }

        var nameId,
            validateAttribute,
            $targetElement,
            respNew = {};

        // Подсветить поля с ошибками
        for (var name in response) {
            nameId = name.replace(/\d-/, "");

            if (scenarios.length > 0 && $.inArray(nameId, scenarios) < 0) {
                continue;
            }

            validateAttribute = {};
            for (var validateField in validateFields) {

                var validateFieldId = validateFields[validateField].id;

                if (validateFieldId.substr(-1) == '*') {
                    if (nameId.search(validateFieldId.substring(0, nameId.length - 1)) >= 0) {
                        validateFieldId = nameId;
                    }
                }

                if (nameId == validateFieldId) {
                    validateAttribute = validateFields[validateField];
                    break;
                }
            }

            if (validateAttribute) {
                $targetElement = formApi.targetForm.find('#' + nameId);
                respNew[nameId] = response[name];
                if (!$targetElement.val() && settingsDefaults.keyUpValidateField && settingsDefaults.keyUpValidateField != nameId) {
                    continue;
                }
                var formGroup = $targetElement.parents(validateAttribute.formGroupTarget)
                    .addClass(validateAttribute.errorCssClass)
                    .removeClass(validateAttribute.successCssClass)
                    .find('.' + validateAttribute.helpCssClass).html(response[name]);
            }
        }

        // Подсветить поля без ошибок
        for (var name in validateFields) {
            nameId = validateFields[name].id;
            validateAttribute = {};

            if (scenarios.length > 0 && $.inArray(nameId, scenarios) < 0) {
                continue;
            }

            if (nameId.substr(-1) == '*') {
                validateAttribute = formApi.validateFields[name];
                nameId = nameId.substring(0, nameId.length - 1);
                $targetElement = formApi.targetForm.find('\[id ^= "' + nameId + '"\]');
                $targetElement.each(function () {
                    var $this = $(this);
                    if (!respNew[$this.attr('id')]) {
                        $this.parents(validateAttribute.formGroupTarget)
                            .addClass(validateAttribute.successCssClass)
                            .removeClass(validateAttribute.errorCssClass)
                            .find('.' + validateAttribute.helpCssClass).html('');

                    }
                });
                continue;
            }

            if (!respNew[nameId]) {
                validateAttribute = validateFields[name];

                $targetElement = formApi.targetForm.find('#' + nameId);

                if (!$targetElement.val() && settingsDefaults.keyUpValidateField && settingsDefaults.keyUpValidateField != nameId) {
                    continue;
                }

                $targetElement.parents(validateAttribute.formGroupTarget)
                    .addClass(validateAttribute.successCssClass)
                    .removeClass(validateAttribute.errorCssClass)
                    .find('.' + validateAttribute.helpCssClass).html('');
            }
        }
    };

})(window.jQuery);