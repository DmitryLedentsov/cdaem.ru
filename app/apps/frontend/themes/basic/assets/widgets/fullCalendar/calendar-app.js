(function($) {
    "use strict";
    $('#calendar').fullCalendar({
        lang: 'ru',
        nextDayThreshold: '00:00:00',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month'
        },
        buttonText: {
            today: 'Сегодня'
        },
        eventLimit: true,
        eventLimitText: function(n) {
            return "Еще +" + n;
        },
        events: {
            url: '/partners/ajax/calendar'
        },
        dayClick: function(date, jsEvent, view) {
            $.get("/partners/ajax/calendar-selected", {'date' : date.format()}, function (response) {
                $('#modal-realty-objects-by-calendar').remove();
                $('body').append(response);
                $('#modal-realty-objects-by-calendar').modal('show');
            });
        },
        eventRender: function(event, element) {
            element.find('.fc-content').html('<span>'+event.title+'</span>');
            element.attr('title', event.address);
        }
    });
}(jQuery));