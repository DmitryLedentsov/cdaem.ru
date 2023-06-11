var $preloadFlag = false; // Флаг загрузки, чтобы избежать повторных запросов
var $targetSelectedAdvertModalTitle;
var $serviceData = {}; // Данные сервиса
var $responseCache = null; // Кэш ответа от сервера


$(function () {
    /**
     * Получаем город по ip
     */
    var currentUserKey = 'currentUser',
        currentUserJson = localStorage.getItem(currentUserKey),
        city, cityId, currentUser;

    var reloadUserInfo = function () {
        currentUserJson = localStorage.getItem(currentUserKey);
        currentUserJson && (currentUser = JSON.parse(currentUserJson));
    };

    if (currentUserJson) {
        currentUser = JSON.parse(currentUserJson);
        $('#locationSelection').text(currentUser.city.name);
    }
    else {
        $.getJSON("/geo/ajax/get-city-by-ip/", {}, function (data) {
            city = data.hasOwnProperty('city') ? data.city : '';
            cityId = data.hasOwnProperty('cityId') ? data.cityId : '';

            // console.log(city, cityId);
            localStorage.setItem(currentUserKey, JSON.stringify({ 'city': {
                'id': cityId,
                'name': city
            }}));

            $('#locationSelection').text(city);
        });
    }

    /**
     * Готовымим окно выбора города
     */

    var cityField = $(".modal-location-city"),
        searchCityURL = "/geo/ajax/select-city-by-api/";

    $(document).on('click', '#locationSelection', function () {
        reloadUserInfo();
        var currentCityId = currentUser ? currentUser.city.id : 0;

        $.getJSON("/geo/ajax/get-popular-cities/", {}, function (cities) {
            cityField.focus();
            var cityList = $('.modal-city-list');
            cityList.html('');
            cities.forEach(function (city) {
                cityList.append(
                    $('<li>', { class: 'modal-city-item' }).append(
                        $('<a>', { class: 'modal-city-link', style: currentCityId === city.city_id ? 'color: #cd8700' : '' }).html(city.name)
                    ).click(function () {
                        $('#locationSelection').text(city.name);

                        localStorage.setItem(currentUserKey, JSON.stringify({ 'city': {
                            'id': city.city_id,
                            'name': city.name
                        }}));

                        $('#modalLocation').modal('hide');
                    })
                );
            })
        });
    });

    cityField.autoComplete({
        resolver: 'custom',
        minLength: 2,
        noResultsText: 'Город не найден',
        formatResult: function (item) {
            return {
                value: item.text,
                text: item.text,
                html: '<div>' + item.text + '</div>',
            };
        },
        change: function (event, ui) {
            // console.log("change");
        },

        events: {
            change: function (event, ui) {
            },
            search: function (qry, callback) {
                $.getJSON(searchCityURL, {'name': qry}, function (data) {
                    // console.log(data);
                    let result = [];
                    data.forEach(item => {
                        result.push({
                            text: item.name,
                            city_id: item.city_id
                        });
                    });

                    console.log({result});
                    callback(result);
                });
            },
        }
    });

    cityField.on('autocomplete.select', function (evt, item) {
        // console.log(item);
        if (item.text) {
            $('#locationSelection').text(item.text);
            localStorage.setItem(currentUserKey, JSON.stringify({ 'city': {
                'id': item.city_id,
                'name': item.text
            }}));
        }
    });





    /**
     * Выбор способа оплаты из самых популярных
     */
    $(document).on('click', '.payments-method-logo', function (event) {
        event.preventDefault();
        var $this = $(this);

        $('.payments-method-logo').removeClass('active');
        $this.addClass('active');

        $(".payment-service select[name='Pay[system]'] option[value='" + $this.data('system') + "']").prop('selected', true);
        $this.parents('.payment-service').find('input[name="Pay[system]"]').val($this.data('system'));
    });


    /**
     * Связь списка и основных значков
     */
    $(".payment-service select[name='Pay[system]']").change(function () {
        var $this = $(this);
        $('.payments-method-logo').removeClass('active');
        $('.payments-method-logo[data-system="' + $this.val() + '"]').addClass('active');
    });


    /**
     * Социальные настройки
     */
    $('.settings-nav .settings').on('click', function (event) {
        var $this = $(this);
        if (!$preloadFlag) {
            $preloadFlag = true;
            $.post("/office/ajax/social", {
                type: $this.data('type'),
                interlocutor: $this.data('interlocutor')
            }, function (response) {
                if ($.isPlainObject(response) && 'status' in response) {
                    if (response.status == 1) {
                        showStackInfo('Информация', response.message);
                        window.location.reload();
                    } else {
                        showStackError('Ошибка', response.message);
                    }
                }
            // }).complete(function () {
            }).done(function () {
                $preloadFlag = false;
            });
        }
    });

    /**
     * Удаление рекламы слайдера
     */
    $('.delete-top-slider').on('click', function (event) {

        if (!confirm('Подтверждение')) {
            return false;
        }

        var $this = $(this);
        // if (!$preloadFlag) {
        if (1) {
            $preloadFlag = true;
            $.post("/office/ajax/delete-top-slider/" + $this.data('advertisement_id'), function (response) {
                if ($.isPlainObject(response) && 'status' in response) {
                    if (response.status == 1) {
                        showStackInfo('Информация', response.message);
                        window.location.reload();
                    } else {
                        showStackError('Ошибка', response.message);
                    }
                }
            // }).complete(function () { // function complete not found
            }).done(function () {
                $preloadFlag = false;
            });
        }
    });


    /**
     * После закрытия окна убрать активность сервисов
     */
    $('#modal-realty-objects-by-service').on('hidden.bs.modal', function () {
        $('#services .service').removeClass('active');
    });


    /**
     * Услуги
     */
    $('#services .service[data-type]').on('click, mouseup', function (event) {
        var $this = $(this);
        if ($('#services').hasClass('min')) {
            location.href = '/office/services#' + $this.data('type');
            return false;
        }
        // Это из старой части
        openWindowByService($this);
        return false;
    });


    // Открыть окно для оплаты сервиса
    var anchor = window.location.hash.replace("#", "");
    if (anchor != "") {
        var $service = $('#services .service[data-type="' + anchor + '"]');
        if ($service.length) {
            openWindowByService($service);
        }
    }


    /**
     * Выбор объектов
     */
    $(document).on("click", '#modal-realty-objects-by-service .advert-preview', function (event) {
        event.preventDefault();
        var $this = $(this);
        $this.toggleClass('selected');
        $serviceData.selected = [];
        $('#modal-realty-objects-by-service').find('.advert-preview.selected').each(function (index) {
            if ($(this).data('advert')) {
                $serviceData.selected.push($(this).data('advert'));
            } else if ($(this).data('apartment')) {
                $serviceData.selected.push($(this).data('apartment'));
            }
        });
        $('#selected-advert-count').html($serviceData.selected.length);

        var $selectedAdvertInfo = $('#selected-advert-info');
        if ($serviceData.selected.length > 0) {
            if (!$selectedAdvertInfo.length) {
                $('#modal-realty-objects-by-service').find('.load').append('<div class="text-center"><span class="btn btn-primary btn-special" id="selected-advert-info">Рассчитать стоимость</span></div>');
            }
        } else {
            if ($selectedAdvertInfo.length) {
                $selectedAdvertInfo.parent().remove();
            }
        }

        $responseCache = $('#modal-realty-objects-by-service').find('.load').html();
    });


    /**
     * Информация о покупке услуги
     */
    $(document).on("click", '#selected-advert-info', function (event) {
        console.log("calc-price");
        event.preventDefault();
        var $this = $(this);
        var $targetModal = $('#modal-realty-objects-by-service');
        $serviceData.days = $('#realty-objects-by-service-days').val();
        $serviceData.date = $('#realty-objects-by-service-date').val();

        // $serviceData.advertisementId = 124; // для теста

        $serviceData.request = 'calc';

        console.log('serviceData', $serviceData);

        // if (!$preloadFlag) {
        if (1) {
            // $preloadFlag = true;
            console.log('post-запрос данных с расчётом цены');

            console.log('токен перед отправкой', $("head > meta[name='csrf-token']").prop('content'));
            $serviceData._csrf = $("head > meta[name='csrf-token']").prop('content');

            $.post("/partners/ajax/buy-service", $serviceData, function (response) {
                $targetModal.find('.load').html(response);
                // $targetModal.find('.modal-title').html('Калькулятор');

                if (!$targetModal.find('.alert-danger').length) {
                    $targetModal.find('.load').append('<div class="text-center"><span class="btn btn-primary btn-special" id="selected-advert-ago">Назад</span> &nbsp; <span class="btn btn-orange btn-special" id="selected-advert-pay">Оплатить</span></div>');
                }
            });
        }
    });


    /**
     * Вернуться назад
     */
    $(document).on("click", '#selected-advert-ago', function (event) {
        event.preventDefault();

        var $this = $(this);
        var $targetModal = $('#modal-realty-objects-by-service');
        $targetModal.find('.modal-title').html($targetSelectedAdvertModalTitle);
        $targetModal.find('.load').html($responseCache);

        refreshScripts();

        $('#realty-objects-by-service-days').val($serviceData.days);
        $('#realty-objects-by-service-date').val($serviceData.date);
    });


    /**
     * Оплата услуги
     */
    $(document).on("click", '#selected-advert-pay', function (event) {
        console.log('Нажали кнопку «Оплатить» на первой форме');
        event.preventDefault();
        var $this = $(this);
        var $targetModal = $('#modal-realty-objects-by-service');

        $serviceData.request = 'payment';

        // console.log($serviceData);

        // if (!$preloadFlag) {
        if (1) {
            // $preloadFlag = true;
            $.post("/partners/ajax/buy-service", $serviceData, function (response) {
                $targetModal.find('.load').html(response);
                $targetModal.find('.modal-title').html('Оплата');
                // }).complete(function () {
            }).done(function () {
                // $preloadFlag = false;
            });
        }
    });


    /**
     * Выбор типов аренды при выборе объектов
     */
    $(document).on('change', '#realty-objects-by-service-rent-type-list', function () {
        var $this = $(this);
        var $target = $this.parents('.office');
        $target.find('.alert-warning').remove();
        if ($this.val()) {
            $target.find('.item').hide();
            var $targetItems = $target.find('[data-rent-type="' + $this.val() + '"]').parents('.item');
            $targetItems.show();
            if (!$targetItems.length) {
                $target.find('.apartment-list').prepend('<div class="alert alert-warning">В данном типе аренды нет объектов.</div>');
            }
        } else {
            $target.find('.item').show();
        }
    });

});

/**
 * Окна для оплаты сервиса
 * Это из старой части сайта
 * @param $this
 * @returns {boolean}
 */
function openWindowByService($this) {
    console.log('openWindowByService', $preloadFlag);
    if (!$preloadFlag) {
        $preloadFlag = true;
        $('#services .service').not($this).removeClass('active');
        $this.toggleClass('active');
        $serviceData.service = $this.data('type');
        $.get("/partners/ajax/realty-objects-by-service", {service: $serviceData.service}, function (response) {
            $('#modal-realty-objects-by-service').remove();
            $('body').append(response);
            $('#modal-realty-objects-by-service').find('.advert-preview').removeClass('selected');
            $('#modal-realty-objects-by-service').modal('show');
            $targetSelectedAdvertModalTitle = $('#modal-realty-objects-by-service').data('title');
        // }).complete(function () { //$.get(...).complete is not a function
        }).done(function () {
            $preloadFlag = false;
        });
    }
}

$(window).scroll(function () {
    if ($(this).scrollTop() > 62) {
        $('#navigatio').addClass('fixed');
    } else if ($(this).scrollTop() < 62) {
        $('#navigatio').removeClass('fixed');
    }
});

$("#opendescription2").click(function () {
    $("#information-advert2").slideToggle("slow", function () {

    });
});

const getToken = async function () {
    let response = await fetch('/users/user/get-csrf-token');
    return response.ok ? await response.text() : false;
};

const updateToken = async function() {
    let token = await getToken();
    token && ($("head > meta[name='csrf-token']").prop('content', token));
};

setInterval(async () => {
    console.log('token update by timeout');
    updateToken();
}, 1000 * 60 * 60 * 15);
