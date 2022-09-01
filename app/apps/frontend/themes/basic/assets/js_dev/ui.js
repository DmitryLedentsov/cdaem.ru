jQuery(function () {

    /**
     * @return string
     */
    function getCsrfParam() {
        return $('meta[name=csrf-param]').attr('content');
    }


    /**
     * @return string
     */
    function getCsrfToken() {
        return $('meta[name=csrf-token]').attr('content');
    }


    // automatically send CSRF token for all AJAX requests
    $.ajaxPrefilter(function (options, originalOptions, xhr) {
        if (!options.crossDomain && getCsrfParam()) {
            xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
        }
    });


    /**
     * AJAX ERROR
     */
    $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
        console.log('ajaxError');
        console.log(thrownError);
        if (thrownError != 'Found' && thrownError != 'abort' && thrownError != 'Permanent Redirect') {
            showStackError('Ошибка', 'Возникла критическая ошибка');
        }
    });


    /**
     * AJAX start
     */
    $(document).ajaxStart(function (event, xhr, settings) {
        // Добавить лоадер
        $('body').append('<div class="bg-loader"></div>');
    });

    /**
     * AJAX redirection
     */
    $(document).ajaxComplete(function (event, xhr, settings) {

        console.log('ajaxComplete');

        // Удалить лоадер
        $('.bg-loader').remove();
        var url = xhr.getResponseHeader('X-Redirect');
        if (url) {
            window.location.href = url;
        }
    });

    initScriptFilter();

    initPickers(true);

    initSelectPicker();


    /**
     * Открытие модального окна
     */
    $(document).on('shown.bs.modal', '.modal', function (e) {

        var $this = $(this);

        if (!$this.find('.modal-content').length) {

            var title = $this.data('title') ? $this.data('title') : "&nbsp;";
            var header = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"></button> <h4 class="modal-title">' + title + '</h4> </div>';

            $this
                .wrapInner('<div class="modal-content"></div>')
                .wrapInner('<div class="modal-dialog"></div>');

            $this.find('.modal-content').wrapInner('<div class="modal-body"></div>');
            $this.find('.modal-content').prepend(header);
        }
    });


    $(document).on('shown.bs.modal', '.modal-2', function (e) {

        var $this = $(this);

        if (!$this.find('.modal-content-2').length) {

            var title = $this.data('title') ? $this.data('title') : "&nbsp;";
            var header = '<div class="modal-header"></div>';
            var footer = '<div class="modal2-footer"><button type="button" class="close" data-dismiss="modal" aria-label="Close">закрыть</button></div>';
            $this
                .wrapInner('<div class="modal-content-2"></div>')
                .wrapInner('<div class="modal-dialog"></div>');
            $this.find('.modal-content-2').append(footer);
            $this.find('.modal-content-2').wrapInner('<div class="modal-body"></div>');
            //$this.find('.modal-content').prepend(header);

        }
    });


    /**
     * Редирект на главной при фильтре объявлений
     */
    $('.agency .select-orange').on('change', function () {
        var selectedValue = $(this).find("option:selected").val();
        location.href = selectedValue;
    });

    $('.agency .select-pinkround').on('change', function () {
        var selectedValue = $(this).find("option:selected").val();
        location.href = selectedValue;
    });

    $('.agency .select-darkgray').on('change', function () {
        var selectedValue = $(this).find("option:selected").val();
        location.href = selectedValue;
    });


    /**
     * Фиксированный хеадер
     */
    $('.sticky-header').scrollToFixed({
        'zIndex': 2001
    });


    /**
     * Слайдер спец. предложений
     */
    if ($(".deals").length) {
        // Слайдер
        $(".deals").owlCarousel({
            autoWidth: true,
            loop: true,
            margin: 0,
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
     * Платежные системы
     */
    $(".payments-method").on("click", function (event) {

        event.preventDefault();

        var $this = $(this);

        $(".payments-method").not($this).removeClass('active');
        $this.toggleClass('active');

        $(".payments-form").not($($this.data('target'))).hide();

        $($this.data('target')).slideToggle();

    });


    /**
     *  Опускаямся к якорю
     */
    $('a[href^="#"], span[data-href^="#"]').not('[data-toggle="tab"]').on('click', function (event) {
        var $this = $(this);
        var target = $this.attr('href') ? $($this.attr('href')) : $($this.data('href'));
        var extra = $this.data('extra') ? $this.data('extra') : 0;
        if (target.length) {
            // event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - extra
            }, 500);
        }
    });


    /**
     * Выпадающий список городов
     */
    var $targetDropDownList = $('.city-drop-down-list');

    if ($targetDropDownList.length > 0) {

        $targetDropDownList.autocomplete({
            serviceUrl: $targetDropDownList.data('url'),
            paramName: "name",
            dataType: "JSON",
            minChars: 3,
            showNoSuggestionNotice: true,
            noSuggestionNotice: "Город не найден",
            triggerSelectOnValidInput: false,

            transformResult: function (response) {
                return {
                    suggestions: $.map(response, function (dataItem) {
                        return {
                            value: dataItem.name,
                            city_id: dataItem.city_id,
                            country: dataItem.country,
                            has_metro: dataItem.has_metro,
                            name: dataItem.name_eng
                        };
                    })
                };
            },

            formatResult: function (suggestion, currentValue) {
                $targetButtonSelectCity = $('#button-select-city');
                if ($targetButtonSelectCity.length) {
                    $targetButtonSelectCity.data('city_id', null);
                    $targetButtonSelectCity.data('city_name', null);
                    $targetButtonSelectCity.parent().hide();
                }
                return "<table class='category-item'><tr><td class='name'>" + suggestion.value + "<div class='parent'>" + suggestion.country + "</div></td></tr></table>";
            },

            onSelect: function (suggestion) {
                var $targetButtonSelectCity = $('#button-select-city');
                if ($targetButtonSelectCity.length) {
                    $targetButtonSelectCity.data('city_id', suggestion.city_id);
                    $targetButtonSelectCity.data('city_name', suggestion.country + ', ' + suggestion.value);
                    $targetButtonSelectCity.parent().show();
                }

                var $selectCityChangeFormAction = $('#select-city-change-form-action');
                if ($selectCityChangeFormAction.length) {
                    var action = $selectCityChangeFormAction.attr('action');
                    var uri = new URI(action);
                    var subdomain = uri.subdomain();
                    uri.subdomain(suggestion.name);
                    $selectCityChangeFormAction.attr('action', uri.href());
                }

                // Метро
                var $targetMetroSelection = $('#metro-selection');
                if ($targetMetroSelection.length) {
                    if (suggestion.has_metro) {
                        $targetMetroSelection.removeClass('hidden');
                    } else {
                        $targetMetroSelection.addClass('hidden');
                    }
                }
            }
        });
    }


    /**
     * Выбрать город
     */
    $(document).on("click", "#button-select-city", function (event) {
        event.preventDefault();
        var $this = $(this);

        if ($this.data('city_id')) {
            $('#' + $this.data('target-city-id')).val($this.data('city_id'));
            $($this.data('target-select-city')).html($this.data('city_name'));
            $('#modal-select-city').modal('hide');
        }
    });


    /**
     * Карта метро
     */
    var metroMskUrl = '/geo/metro-msk';

    var targetModal = $('#modal-subway-map');
    var targetMap = $('#modal-subway-map .map');
    var targetList = $('#metro-selected-list');
    var selectMetroStations = [];
    var cacheMap = null;

    // Карта метро
    $('#open-subway-map').on('click', function (event) {

        var $this = $(this);

        var updateCheckedMetroList = function () {
            targetList.find('p').each(function () {
                var $this = $(this);
                $('#modal-subway-map').find('circle#' + $this.attr('id')).attr('data-active', 1);
            });
        };

        targetModal.modal('show');

        // Загрузка
        targetMap.html('<div class="loader" style="margin: auto;"></div>');
        targetModal.find('.form-group').hide();

        if (cacheMap) {
            targetModal.find('.form-group').show();
            targetMap.html(cacheMap);
            updateCheckedMetroList();
        } else {
            $.post(metroMskUrl, function (response) {
                cacheMap = response;
                targetModal.find('.form-group').show();
                targetMap.html(cacheMap);
                updateCheckedMetroList();
            });
        }

        return false;
    });


    /**
     * Выбор станции метро
     */
    $(document).on("mouseup", "#modal-subway-map .map circle.station, #modal-subway-map .map text.station", function () {

        var $this = $(this);

        if ($this.get(0).tagName == 'text') {
            $this = $this.parents('svg').find('circle#' + $this.attr('id'));
        }

        var active = 0;
        if (parseInt($this.attr('data-active')) === 1) {
            active = 1;
        }

        if (active) {
            $this.attr('data-active', 0);
        } else {
            $this.attr('data-active', 1);
        }

        if (targetList.length > 0) {
            if (active) {
                $this.attr('data-active', 0);
                targetList.find('#' + $this.attr('id')).remove();
            } else {
                $this.attr('data-active', 1);
                targetList.append('<p id="' + $this.attr('id') + '"> <span class="remove">X</span> ' + $this.attr('title') + ' <input type="hidden" name="' + targetList.data('input-name') + '" value="' + getMetroId($this.attr('id')) + '" /></p>');
            }
        } else {

            var metroId = getMetroId($this.attr('id'));

            /*if (active) {

             var key = selectMetroStations.indexOf(metroId));

             if (key >= 0) {
             selectMetroStations.splice(key, 1);
             }

             } else {
             selectMetroStations.push(metroId);
             }*/

            if (active) {
                selectMetroStations.forEach(
                    function (element, index, array) {
                        if (element[0] == metroId) {
                            selectMetroStations.splice(index, 1);
                            return false;
                        }
                    }
                );
            } else {
                selectMetroStations.push([metroId, $this.attr('title')]);
            }
        }

        // Обновить кэш схемы метро
        cacheMap = $this.parents('#modal-subway-map .map').html();

        return false;
    });


    /**
     * Удалить выбранную станцию метро
     */
    $(document).on("click", '#metro-selected-list .remove', function () {

        var $this = $(this);

        targetMap.find("circle#" + $this.parent('p').attr('id')).attr('data-active', '0');
        $this.parent('p').remove();

        // Обновить кэш схемы метро
        cacheMap = targetMap.html();

        return false;
    });


    /**
     * Удалить выбранную станцию метро
     */
    $(document).on("click", '#metro-selected-list .remove', function () {

        var $this = $(this);

        targetMap.find("circle#" + $this.parent('p').attr('id')).attr('data-active', '0');
        $this.parent('p').remove();

        // Обновить кэш схемы метро
        cacheMap = targetMap.html();

        return false;
    });


    /**
     * Закрыть окно
     */
    $(document).on("click", '.close-modal', function () {
        $('.modal').modal('hide');

        if (targetList.length <= 0) {
            if (selectMetroStations.length > 0) {

                var metroIds = [];
                var metroString = [];

                selectMetroStations.forEach(
                    function (element, index, array) {

                        if (metroIds.indexOf(element[0]) == -1) {
                            metroIds.push(element[0]);

                            var name = toTranslit(element[1]);
                            name = name.split('-');
                            name = name[0] + '-' + name[1];

                            metroString.push(element[0] + '_' + name);
                        }
                    }
                );

                var $targetSubwayMap = $('#open-subway-map');

                if ($targetSubwayMap.length) {
                    document.location.href = $targetSubwayMap.data('url') + '/?metro=' + metroString;
                } else {
                    document.location.href = '/?metro=' + metroString;
                }
            }
        }

        return false;
    });


    /**
     * Заказ такси
     */
    $(document).on("click", '#taxi', function () {
        $.get('/msk-taxi', function (response) {
            $('#modal-taxi').remove();
            $('body').append(response);
            $('#modal-taxi').modal('show');
        });
        return false;
    });

});


/**
 * Инициализация выбора даты и времени
 */
function initPickers(minDate) {

    if (minDate == true) {
        if (typeof moment == 'function') {
            minDate = moment();
        } else {
            minDate = false;
        }
    } else {
        minDate = false;
    }

    /**
     * Дата и время
     */
    var $targetDatepicker = $('.datepicker');
    var $targetTimepicker = $('.timepicker');

    if ($targetDatepicker.length) {
        $targetDatepicker.datetimepicker({
            format: 'DD.MM.YYYY',
            locale: 'ru',
            minDate: minDate,
            showTodayButton: true,
            showClear: true,
            ignoreReadonly: true,
            icons: {
                time: 'icon-time',
                date: 'icon-calendar',
                up: 'icon-chevron-up',
                down: 'icon-chevron-down',
                previous: 'icon-chevron-left',
                next: 'icon-chevron-right',
                today: 'icon-screenshot',
                clear: 'icon-trash'
            }
        }).on('dp.show', function (ev) {
            $(ev.currentTarget).parent().find('a[data-action=today]').html('Сегодня').addClass('today');
            $(ev.currentTarget).parent().find('a[data-action=clear]').html('Сбросить').addClass('clear');
        });
    }


    if ($targetTimepicker.length) {
        $targetTimepicker.datetimepicker({
            format: 'HH:mm',
            showClear: true,
            ignoreReadonly: true,
            stepping: 5,
            icons: {
                time: 'icon-time',
                date: 'icon-calendar',
                up: 'icon-chevron-up',
                down: 'icon-chevron-down',
                previous: 'icon-chevron-left',
                next: 'icon-chevron-right',
                today: 'icon-screenshot',
                clear: 'icon-trash'
            }
        }).on('dp.show', function (ev) {
            $(ev.currentTarget).parent().find('a[data-action=clear]').html('Сбросить').addClass('clear');
        });
    }
}


/**
 * Информационное уведомление
 */
function showStackInfo(title, text, callbackAfterClose) {
    var modal_overlay;
    if (typeof info_box != "undefined") {
        info_box.open();
        return;
    }
    new PNotify({
        title: title,
        text: text,
        type: "info",
        //icon: "fa fa-info-circle",
        delay: 24500,
        history: {
            history: true
        },
        buttons: {
            closer: false,
            sticker: false
        },
        after_close: callbackAfterClose
    });
}


/**
 * Уведомление об ошибке
 */
function showStackError(title, text) {
    var modal_overlay;
    if (typeof info_box != "undefined") {
        info_box.open();
        return;
    }
    new PNotify({
        title: title,
        text: text,
        type: "error",
        //icon: "fa fa-info-circle",
        delay: 4500,
        history: {
            history: true
        },
        buttons: {
            closer: false,
            sticker: false
        }
    });
}


/**
 * Запуск виджета быстрой оплаты
 * @param service
 * @param data
 */
function fast_payment_widget(service, data) {
    $.post('/merchant/payment-widget', $.extend({service: service}, {'data': data}), function (response) {
        // $('#modal-payment-widget').remove();
        // $('body').append(response);
        // $('#modal-payment-widget').modal('show');

        window.openWindow('Оплата', response, 'large');
    });
}


/**
 * Скролл поднимается к ошибке
 * @param response
 * @param validateFields
 */
function scrollToFirstError(response, validateFields) {
    var firstIdError = null;

    for (var field in validateFields) {
        for (var element in response) {

            if (validateFields[field].id.substr(-1) == '*') {
                if (element.search(validateFields[field].id) != -1) {
                    firstIdError = element;
                    break;
                }
            }

            if (validateFields[field].id == element) {
                firstIdError = element;
                break;
            }
        }
        if (firstIdError) {
            break;
        }
    }


    if (firstIdError) {
        var extraHeight = $('.sticky-header.scroll-to-fixed-fixed').height() || 0;
        var destination = $('#' + firstIdError).parents('.form-group').offset().top - extraHeight - 15;
        $('html, body').stop().animate({scrollTop: destination}, 600);
    }
}


var reloadableScripts = [];

/**
 * Предотвратить загрузку повторных скриптов
 */
function initScriptFilter() {
    var hostInfo = location.protocol + '//' + location.host;
    var loadedScripts = $('script[src]').map(function () {
        return this.src.charAt(0) === '/' ? hostInfo + this.src : this.src;
    }).toArray();

    $.ajaxPrefilter('script', function (options, originalOptions, xhr) {
        if (options.dataType == 'jsonp') {
            return;
        }
        var url = options.url.charAt(0) === '/' ? hostInfo + options.url : options.url;
        if ($.inArray(url, loadedScripts) === -1) {
            loadedScripts.push(url);
        } else {
            var found = $.inArray(url, $.map(reloadableScripts, function (script) {
                return script.charAt(0) === '/' ? hostInfo + script : script;
            })) !== -1;
            if (!found) {
                xhr.abort();
            }
        }
    });

    $(document).ajaxComplete(function (event, xhr, settings) {
        var styleSheets = [];
        $('link[rel=stylesheet]').each(function () {
            if ($.inArray(this.href, reloadableScripts) !== -1) {
                return;
            }
            if ($.inArray(this.href, styleSheets) == -1) {
                styleSheets.push(this.href)
            } else {
                $(this).remove();
            }
        })
    });
}


function initSelectPicker() {
    /**
     * Кастомные селекты
     */
    $('.select-orange').selectpicker({
        style: 'btn-orange',
        liveSearch: false,
        maxOptions: 1,
        showIcon: false,
        showContent: false,
        iconBase: '',
        tickIcon: ''
    });

    $('.select-pinkround').selectpicker({
        style: 'btn-pinkround',
        liveSearch: false,
        maxOptions: 1,
        showIcon: false,
        showContent: false,
        iconBase: '',
        tickIcon: ''
    });

    $('.select-darkgray').selectpicker({
        style: 'btn-darkgray',
        liveSearch: false,
        maxOptions: 1,
        showIcon: false,
        showContent: false,
        iconBase: '',
        tickIcon: ''
    });

    $('.select-primary').selectpicker({
        style: 'btn-primary',
        liveSearch: false,
        maxOptions: 1,
        showIcon: false,
        showContent: false,
        iconBase: '',
        tickIcon: ''
    });

    $('.select-white').selectpicker({
        style: 'btn-white',
        liveSearch: false,
        maxOptions: 1,
        showIcon: false,
        showContent: false,
        iconBase: '',
        tickIcon: ''
    });

}


/**
 * Транслитерация
 * @param text
 * @returns {*}
 */
function toTranslit(text) {
    return text.replace(/([а-яё])|([\s_-])|([^a-z\d])/gi,
        function (all, ch, space, words, i) {
            if (space || words) {
                return space ? '-' : '';
            }
            var code = ch.charCodeAt(0),
                index = code == 1025 || code == 1105 ? 0 :
                    code > 1071 ? code - 1071 : code - 1039,
                t = ['yo', 'a', 'b', 'v', 'g', 'd', 'e', 'zh',
                    'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
                    'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh',
                    'shch', '', 'y', '', 'e', 'yu', 'ya'
                ];
            return t[index];
        });
}


$(window).scroll(function () {
    if ($(this).scrollTop() > 62) {
        $('#navigatio').addClass('fixed');
    } else if ($(this).scrollTop() < 62) {
        $('#navigatio').removeClass('fixed');
    }
});


$("#item-br").click(function () {
    $(".tutut").slideToggle();
    $(".tutut2").css('display', 'none');
});

$("#item-iu").click(function () {
    $(".tutut2").slideToggle();
    $(".tutut").css('display', 'none');
});
$("#item-iu-2").click(function () {
    $(".tutut").slideToggle();
});

$("#item-iu-3").click(function () {
    $(".tutut2").slideToggle();
});

$("#advert-btn").click(function () {
    $(".adverto-list").slideToggle();
});

$("#service-btn").click(function () {
    $(".servico-list").slideToggle();
});

/**
 * Заглушка для устаревших браузеров
 */
// не работает в текущей версии jquery
/*$.reject({
    // Specifies which browsers/versions will be blocked
    reject: {
        all: false, // Covers Everything (Nothing blocked)
        msie: 10 // Covers MSIE <= 6 (Blocked by default)
        /!*
         * Many possible combinations.
         * You can specify browser (msie, chrome, firefox)
         * You can specify rendering engine (geko, trident)
         * You can specify OS (Win, Mac, Linux, Solaris, iPhone, iPad)
         *
         * You can specify versions of each.
         * Examples: msie9: true, firefox8: true,
         *
         * You can specify the highest number to reject.
         * Example: msie: 9 (9 and lower are rejected.
         *
         * There is also "unknown" that covers what isn't detected
         * Example: unknown: true
         *!/
    },
    display: [], // What browsers to display and their order (default set below)
    browserShow: true, // Should the browser options be shown?
    browserInfo: {// Settings for which browsers to display
        chrome: {
            // Text below the icon
            text: 'Google Chrome',
            // URL For icon/text link
            url: 'http://www.google.com/chrome/'
            // (Optional) Use "allow" to customized when to show this option
            // Example: to show chrome only for IE users
            // allow: { all: false, msie: true }
        },
        firefox: {
            text: 'Mozilla Firefox',
            url: 'http://www.mozilla.com/firefox/'
        },
        safari: {
            text: 'Safari',
            url: 'http://www.apple.com/safari/download/'
        },
        opera: {
            text: 'Opera',
            url: 'http://www.opera.com/download/'
        }
    },

    // Pop-up Window Text
    header: 'Did you know that your Internet Browser is out of date?',

    paragraph1: 'Your browser is out of date, and may not be compatible with ' +
        'our website. A list of the most popular web browsers can be ' +
        'found below.',

    paragraph2: 'Just click on the icons to get to the download page',

    // Allow closing of window
    close: true,

    // Message displayed below closing link
    closeMessage: 'By closing this window you acknowledge that your experience ' +
        'on this website may be degraded',
    closeLink: 'Close This Window',
    closeURL: '#',

    // Allows closing of window with esc key
    closeESC: true,

    // Use cookies to remmember if window was closed previously?
    closeCookie: false,
    // Cookie settings are only used if closeCookie is true
    cookieSettings: {
        // Path for the cookie to be saved on
        // Should be root domain in most cases
        path: '/',
        // Expiration Date (in seconds)
        // 0 (default) means it ends with the current session
        expires: 0
    },

    // Path where images are located
    imagePath: './images/',
    // Background color for overlay
    overlayBgColor: '#000',
    // Background transparency (0-1)
    overlayOpacity: 0.8,

    // Fade in time on open ('slow','medium','fast' or integer in ms)
    fadeInTime: 'fast',
    // Fade out time on close ('slow','medium','fast' or integer in ms)
    fadeOutTime: 'fast',

    // Google Analytics Link Tracking (Optional)
    // Set to true to enable
    // Note: Analytics tracking code must be added separately
    analytics: false
});*/


$("#item-br").click(function () {
    $("#main-main").toggle();
});


///$( ".recaptcha-checkbox-checkmark" ).click(function() {
///  $("html, body").animate({ scrollTop: $('div.alert').height() }, 2000);
///});






