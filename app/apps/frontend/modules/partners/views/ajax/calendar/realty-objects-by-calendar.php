<?php
/*
    Стоимость сервиса и подтверждение покупки
    @var \yii\web\View this
    @var string $date
*/
use yii\helpers\Html;
?>
<div id="modal-realty-objects-by-calendar" class="modal fade" data-title="Укажите время аренды">
    <div class="office">
        <div class="load">
            <div class="text-center">
                <button class="btn btn-orange" id="by-date">Информация за <?=date('d.m.Y', strtotime($date))?></button> &nbsp; &nbsp; &nbsp;
                <button class="btn btn-orange" id="by-apartments">Все апаратменты</button>
            </div>
            <p><br/></p>
            <div id="objects-by-calendar">
            </div>
        </div>
    </div>
</div>


<script>

    $(function () {

        $.get( "/partners/ajax/calendar-selected", {'type' : 'info', 'date' : '<?=$date?>'}, function( response ) {
            $('#objects-by-calendar').html(response);
        });

        $('#by-date').on('click', function () {
            $.get( "/partners/ajax/calendar-selected", {'type' : 'info', 'date' : '<?=$date?>'}, function( response ) {

                $('#objects-by-calendar').html(response);

            });
        });

        $('#by-apartments').on('click', function () {
            $.get( "/partners/ajax/calendar-selected", {'type' : 'selected', 'date' : '<?=$date?>'}, function( response ) {
                $('#objects-by-calendar').html(response);
                init();
            });
        });

        function init() {

            var clear = {};

            $('.object-type').each(function( index ) {
                var $this = $(this);
                if ($this.val() == -1) {
                    $this.parents('.options').find('.options-date').hide();
                }
                clear[$this.data('apartment_id')] = $this.val();
            });

            var previous = null;


            $('.object-type').on('focus', function () {
                previous = this.value;
            }).change(function() {

                var $this = $(this);
                if ($this.val() == -1 || previous == -1) {
                    if (clear[$this.data('apartment_id')] == -1) {
                        $('#form-calendar').data('formApi').reset('calenderform-date_start-' + $this.data('apartment_id'));
                        $('#form-calendar').data('formApi').reset('calenderform-date_end-' + $this.data('apartment_id'));
                    }
                }
                if ($this.val() == -1) {
                    $this.parents('.options').find('.options-date').hide();
                } else {
                    $this.parents('.options').find('.options-date').show();
                    var datepicker = $this.parents('.options').find('.datepicker');
                    datepicker.each(function( index ) {
                        var $this = $(this);
                        if (!$this.val()) {
                            $this.val('<?=date('d.m.Y', strtotime($date))?>');
                        }
                    });
                }

                // Make sure the previous value is updated
                previous = this.value;
            });

            initPickers();

            /**
             * Отправка формы "Добавить отзыв"
             */
            $('#form-calendar').formApi({

                // Все поля
                fields: [
                    "_csrf",
                    "CalenderForm[whichDate]",
                    "CalenderForm[calendarApartmentId]",
                    "CalenderForm[type]",
                    "CalenderForm[date_start]",
                    "CalenderForm[time_start]",
                    "CalenderForm[date_end]",
                    "CalenderForm[time_end]"
                ],

                // Дополнительные поля, будут отправлены по кнопке submit
                extraSubmitFields: {
                    submit: "submit"
                },

                // Валидация полей
                validateFields: [
                    "calenderform-date_start-*",
                    "calenderform-time_start-*",
                    "calenderform-date_end-*",
                    "calenderform-time_end-*"
                ],

                // Событие срабатывает при успешном запросе
                success: function (formApi, response) {
                    if ($.isPlainObject(response) && 'status' in response) {
                         if (response.status == 1) {
                             showStackInfo('Информация', response.message);
                             window.location.reload();
                         } else {
                             showStackError('Ошибка', response.message);
                         }
                    }
                }
            });
        }
    });
</script>