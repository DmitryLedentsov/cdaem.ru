<?php
/*
    Объекты недвижимости для сервисов
    @var \yii\web\View this
    @var string $service
*/

use yii\helpers\Html;
use common\modules\partners\models\Service;

?>
<div id="modal-realty-objects-by-service" class="modal fade" data-title="Выберите объект">
    <div class="office">
        <div class="load">
            <?php try {
    $service = Yii::$app->service->load($service);
    echo Html::tag('p', '<b>' . $service->getName() . '</b>', []); ?>

                <?php if ($service->getId() != Service::SERVICE_ADVERTISING_TOP_SLIDER): ?>
                    <p>Выделено объектов: <b id="selected-advert-count">0</b></p>
                <?php endif; ?>

                <?php if ($service->isTimeInterval()): ?>
                    <p style="margin: 15px 0">
                    <div class="alert alert-info">
                        <b>Внимание:</b>
                        Вы можете указать кол-во дней на которое хотите продлить услугу, а так-же дату активации (услуга
                        начнет действовать с указанной даты)
                        выбранной услуги.
                    </div>
                    </p>
                    <div class="clearfix">
                        <div class="form-group" style="float: left; clear: none; margin-right: 15px;">
                            <label class="control-label">Кол-во дней:</label>
                            <?php echo Html::textInput('days', 1, [
                                'id' => 'realty-objects-by-service-days',
                                'class' => 'form-control',
                                'maxlength' => 2,
                                'style' => 'width: 100px;'
                            ]); ?>
                        </div>
                        <div class="form-group" style="float: left; clear: none; ">
                            <label class="control-label">Дата старта:</label>
                            <?php echo Html::textInput('date', null, [
                                'id' => 'realty-objects-by-service-date',
                                'class' => 'form-control datepicker',
                                'maxlength' => 2,
                                'style' => 'width: 150px;',
                                'readonly' => 'readonly',
                            ]); ?>
                        </div>
                    </div>

                <?php endif; ?>


                <?php if ($service->getId() != Service::SERVICE_APARTMENT_CONTACTS_OPEN): ?>
                    <?php echo Html::dropDownList('rent-type', null, $rentTypeslist, [
                        'id' => 'realty-objects-by-service-rent-type-list',
                        'class' => 'form-control select-white',
                        'prompt' => 'Все',
                    ]); ?>
                    <p><br/></p>
                <?php endif; ?>


                <?php echo \common\modules\partners\widgets\frontend\RealtyObjectsByService\RealtyObjectsByService::widget([
                    'service' => $service->getId(),
                    'userId' => Yii::$app->user->id,
                ])
                ?>

            <?php
} catch (\Exception $e) { ?>
                <div class="alert alert-danger">Возникла критическая ошибка</div>
            <?php } ?>
        </div>
    </div>
</div>


<script>
    function refreshScripts() {

        $('.modal .bootstrap-select').remove();

        if ($('.select-white').length) {
            initSelectPicker();
        }

        if ($('.datepicker').length) {
            $('.datepicker').datetimepicker({
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
                $(ev.currentTarget).parent().find('a[data-action=today]').html('Сегодня').addClass('today');
                $(ev.currentTarget).parent().find('a[data-action=clear]').html('Сбросить').addClass('clear');
            });
        }

    }

    refreshScripts();

</script>