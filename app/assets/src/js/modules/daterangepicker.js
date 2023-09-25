(function ($) {
    "use strict";

    let daterangepickerTranslations = {
        format: 'YYYY-MM-DD H:mm',
        // todayHighlight: true,
        language: "ru",
        separator: "-",
        applyLabel: "Выбрать",
        cancelLabel: "Отмена",
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
        // autoApply: true,
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 15,
        minDate: moment().format('YYYY-MM-DD'),
        maxDate: moment().add(1, 'year').format('YYYY-MM-DD'),
        locale: daterangepickerTranslations,
    });

    $daterangepicker.on('apply.daterangepicker', function(ev, picker) {
        let $this = $(this);

        const parentsForm = $($this).parents("form");
        const formId = parentsForm.length > 0 ? parentsForm[0].id : false;

        // Отчистка валидации
        formId && $($this).find('.form-group .invalid-feedback').prev().each((i, input) => {
            $('#' + formId).displayValidation('clearElement', `[name='${input.name}']`);
        });

        $this.find('.daterangepicker-from .daterangepicker-month').text(picker.startDate.format('D MMMM'));
        $this.find('.daterangepicker-from .daterangepicker-day').text(`, ${picker.startDate.format('dddd')} ${picker.startDate.format('HH:mm')}`);
        $this.find('.daterangepicker-to .daterangepicker-month').text(picker.endDate.format('D MMMM'));
        $this.find('.daterangepicker-to .daterangepicker-day').text(`, ${picker.endDate.format('dddd')} ${picker.endDate.format('HH:mm')}`);

        $this.find('.daterangepicker-from input[type=hidden].date').val(picker.startDate.format('YYYY-MM-DD'));
        $this.find('.daterangepicker-from input[type=hidden].time').val(picker.startDate.format('HH:mm'));
        $this.find('.daterangepicker-to input[type=hidden].date').val(picker.endDate.format('YYYY-MM-DD'));
        $this.find('.daterangepicker-to input[type=hidden].time').val(picker.endDate.format('HH:mm'));
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
