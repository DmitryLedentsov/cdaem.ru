(function ($) {
    "use strict";

    let $targetDatepicker = $('[data-datepicker]');
    let $targetTimepicker = $('[data-timepicker]');

    if ($targetDatepicker.length) {

        $targetDatepicker.datetimepicker({
            inline: true,
            format: 'DD.MM.YYYY',
            locale: 'ru',
            minDate: moment(),
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
            $(ev.currentTarget)
                .parent()
                .find('.bootstrap-datetimepicker-widget a[data-action=today]')
                .html('Сегодня')
                .addClass('today');

            $(ev.currentTarget)
                .parent()
                .find('.bootstrap-datetimepicker-widget a[data-action=clear]')
                .html('Сбросить')
                .addClass('clear');
        });
    }

    if ($targetTimepicker.length) {
        $targetTimepicker.datetimepicker({
            inline: true,
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
            $(ev.currentTarget)
                .parent()
                .find('.bootstrap-datetimepicker-widget .list-unstyled li')
                .first()
                .before(
                    $(ev.currentTarget)
                        .parent()
                        .find('.bootstrap-datetimepicker-widget .list-unstyled li')
                        .last()
                );

            $(ev.currentTarget)
                .parent()
                .find('.bootstrap-datetimepicker-widget a[data-action=clear]')
                .html('Сбросить')
                .addClass('clear');
        });
    }

})(jQuery);