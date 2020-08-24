/* if have no editor (like input, or textarea) then we set refreshing every 10 minutes */
var editorsExist = $('body *').is('textarea, input');
if (!editorsExist) {
    var refresher = document.createElement('meta');
    refresher.setAttribute('http-equiv', 'refresh');
    refresher.setAttribute('content', '600');
    document.head.appendChild(refresher);
}

var changeTimerTitle = null;

function NT($param) {

    var newTxt = "Новые уведомления";
    var oldTxt = document.title;

    function changeTitle() {
        if (document.title == '[---]') {
            document.title = newTxt;
        } else {
            document.title = '[---]';
        }
    }

    if (!changeTimerTitle) {
        changeTimerTitle = setInterval(changeTitle, 800);
        if ($param == false) {
            clearInterval(changeTimerTitle);
            document.title = oldTxt;
        }
    }
}


$(function () {

    var updateInterval = 60000; // Интервал времени в секундах до обновления

    /**
     * Проверка на новые заявки
     */
    setInterval(function () {
        $.getJSON("/admin/default/get-info?url=" + location.href, function (response) {

            if (response.promotion) {

                // Звук
                ion.sound.play("camera_flashing");

                // Сообщение
                var message = '<p>Уведомление:</p>';
                for (var item in response) {
                    if (response[item]['count'] > 0) {
                        message += ' - ' + response[item].message + '<br/>' + 'Всего: ' + response[item].count + '<br/><br/>';
                    }
                }
                $.jGrowl(message, {theme: 'growl-warning', life: 15000, position: 'top-left'});

                // Включить Мигание title
                NT(true);
            } else {
                // Выключить Мигание title
                NT(false);
            }

        });

    }, updateInterval);


    /**
     * Звук
     */
    ion.sound({
        sounds: [
            {name: "camera_flashing"}
            /*
             {name: "beer_can_opening"},
             {name: "bell_ring"},
             {name: "branch_break"},
             {name: "button_click"},
             {name: "button_click_on"},
             {name: "button_push"},
             {name: "button_tiny"},
             {name: "camera_flashing_2"},
             {name: "cd_tray"},
             {name: "computer_error"},
             {name: "door_bell"},
             {name: "door_bump"},
             {name: "glass"},
             {name: "keyboard_desk"},
             {name: "light_bulb_breaking"},
             {name: "metal_plate"},
             {name: "metal_plate_2"},
             {name: "pop_cork"},
             {name: "snap"},
             {name: "staple_gun"},
             {name: "tap"},
             {name: "water_droplet"},
             {name: "water_droplet_2"},
             {name: "water_droplet_3"}*/
        ],
        path: "/ion.sound/sounds/",
        preload: true,
        volume: 1.0
    });


    /**
     * Выпадающий список выбора города
     */
    var $targetSelectCity = $('.select-city');
    var cache = {};
    $targetSelectCity.autocomplete({
        source: function (request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return;
            }
            $.ajax({
                url: _settings.siteDomain,
                data: {name: request.term},
                dataType: 'json',
                crossDomain: true,
                async: true,
                success: function (data) {
                    cache[term] = data;
                    response(data);
                }
                /*beforeSend : function(jqXHR, settings) {
                 jqXHR.setRequestHeader("HTTP_X_REQUESTED_WITH", "XmlHttpRequest");
                 }*/
            });
        },
        select: function (e, ui) {
            e.preventDefault();
            $(this).val(ui.item.city_id);
        },
        delay: 500,
        minLength: 2
    });
    if ($targetSelectCity.data()) {
        $targetSelectCity.each(function (i, element) {
            var ac = $(element).data('ui-autocomplete');
            if (ac) {
                ac._renderItem = function (ul, item) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append('<table><tr><td class="desc">' + item.name + ' <span style="font-size: 80%; color: silver">(' + item.country + ')</span></td></tr></table>')
                        .appendTo(ul);
                };
            }

        });
    }
});