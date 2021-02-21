(function ($) {
    "use strict";

    let daterangepickerTranslations = {
        format: "YYYY-MM-DD",
        separator: "-",
        applyLabel: "Apply",
        cancelLabel: "Cancel",
        fromLabel: "From",
        toLabel: "To",
        customRangeLabel: "Custom",
        daysOfWeek: [
            "пн",
            "вт",
            "ср",
            "чт",
            "пт",
            "сб",
            "вс"
        ],
        monthNames: [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        firstDay: 1,
        dateArrival: 'Дата заезда',
        dateDeparture: 'Дата выезда',
    };

    let $daterangepicker = $('[data-daterangepicker]');

    $daterangepicker.daterangepicker({
        minYear: parseInt(moment().format('YYYY')),
        maxYear: parseInt(moment().format('YYYY')) + 1,
        showDropdowns: true,
        autoApply: true,
        minDate: moment().format('YYYY-MM-DD'),
        maxDate: moment().add(1, 'year').format('YYYY-MM-DD'),
        locale: daterangepickerTranslations,
    });

    $daterangepicker.on('apply.daterangepicker', function(ev, picker) {
        let $this = $(this);
        $this.find('.daterangepicker-from .daterangepicker-month').text(picker.startDate.format('D MMMM'));
        $this.find('.daterangepicker-from .daterangepicker-day').text(picker.startDate.format('dddd'));
        $this.find('.daterangepicker-to .daterangepicker-month').text(picker.endDate.format('D MMMM'));
        $this.find('.daterangepicker-to .daterangepicker-day').text(picker.endDate.format('dddd'));

        $this.find('.daterangepicker-from input[type=hidden]').val(1);
        $this.find('.daterangepicker-to input[type=hidden]').val(2);
    });

    $daterangepicker.on('show.daterangepicker', function(ev, picker) {
        $(this).addClass('is-active');
    });

    $daterangepicker.on('hide.daterangepicker', function(ev, picker) {
        $(this).removeClass('is-active');
    });

    $('.daterangepicker.show-calendar')
        .find('.drp-calendar.left')
        .prepend('<div class="drp-description">' + daterangepickerTranslations.dateArrival + '</div>');

    $('.daterangepicker.show-calendar').find('.drp-calendar.right')
        .prepend('<div class="drp-description">' + daterangepickerTranslations.dateDeparture + '</div>');

})(jQuery);