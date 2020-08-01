<?php
/*
    Форма заказа такси
    @var \yii\web\View this
    @var common\modules\site\models\Taxi $model
*/

use yii\helpers\Html;

?>

<div class="modal fade" id="modal-taxi" data-title="Заказать такси в Москве">

    <?php echo Html::beginForm(['/site/default/taxi'], 'POST', ['id' => 'form-taxi', 'class' => 'horizontal']) ?>

    <div class="form-group required">
        <label class="control-label" style="width: 120px"><?php echo $model->getAttributeLabel('name') ?></label>
        <?php echo Html::activeTextInput($model, 'name', ['class' => 'form-control', 'style' => 'width: 50%']) ?>
        <div class="help-block" style="padding-left: 120px"></div>
    </div>

    <div class="form-group required">
        <label class="control-label" style="width: 120px"><?php echo $model->getAttributeLabel('phone') ?></label>
        <?php echo Html::activeTextInput($model, 'phone', ['class' => 'form-control', 'placeholder' => '+7 (___) ___-____', 'style' => 'width: 50%']) ?>
        <div class="help-block" style="padding-left: 120px"></div>
    </div>

    <div class="form-group required">
        <label class="control-label"
               style="width: auto"><?php echo $model->getAttributeLabel('departureAddress') ?></label>
        <?php echo Html::activeTextInput($model, 'departureAddress', ['class' => 'form-control']) ?>
        <div class="help-block"></div>
    </div>

    <div class="form-group required">
        <label class="control-label"
               style="width: auto"><?php echo $model->getAttributeLabel('destinationAddress') ?></label>
        <?php echo Html::activeTextInput($model, 'destinationAddress', ['class' => 'form-control']) ?>
        <div class="help-block"></div>
    </div>

    <div class="form-group required">
        <label class="control-label" style="width: 215px"><?php echo $model->getAttributeLabel('carClass') ?></label>
        <?php echo Html::activeDropDownList($model, 'carClass', $model->getCarClassArray(), ['class' => 'form-control', 'style' => 'width: 51%']) ?>
        <div class="help-block" style="padding-left: 215px"></div>
    </div>

    <div class="form-group required">
        <label class="control-label" style="width: 215px;"><?php echo $model->getAttributeLabel('date') ?></label>
        <div class="clearfix">
            <?php echo Html::activeHiddenInput($model, 'date_delivery') ?>
            <div style="width:125px; float: left; margin-right: 5px;"><?php echo Html::activeTextInput($model, 'date', ['class' => 'form-control datepicker', 'readonly' => 'readonly']) ?></div>
            <div style="width:85px; float: left"><?php echo Html::activeTextInput($model, 'time', ['class' => 'form-control timepicker', 'readonly' => 'readonly']) ?></div>
        </div>
        <div class="help-block" style="padding-left: 215px"></div>
    </div>

    <p><br/></p>

    <div class="form-group text-center">
        <input value="Отправить" class="btn btn-primary" type="submit">
    </div>

    <?php echo Html::endForm() ?>

</div>


<script type="text/javascript">

    $(function () {

        /**
         * Отправка формы "Добавить отзыв"
         */
        $('#form-taxi').formApi({

            // Все поля
            fields: [
                "_csrf",
                "Taxi[name]",
                "Taxi[phone]",
                "Taxi[departureAddress]",
                "Taxi[destinationAddress]",
                "Taxi[carClass]",
                "Taxi[time]",
                "Taxi[date]"
            ],

            // Дополнительные поля, будут отправлены по кнопке submit
            extraSubmitFields: {
                submit: "submit"
            },

            // Валидация полей
            validateFields: [
                "taxi-name",
                "taxi-phone",
                "taxi-departureaddress",
                "taxi-destinationaddress",
                "taxi-carclass",
                "taxi-date_delivery"
            ],

            // Событие срабатывает при успешном запросе
            success: function (formApi, response) {
                if ($.isPlainObject(response) && 'status' in response) {
                    if (response.status == 1) {
                        formApi.targetForm.parent().html('<div class="alert alert-success">' + response.message + '</div>');
                    } else {
                        formApi.targetForm.parent().html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                }
            }
        });
    });

    /**
     * Маска для телефона
     */
    $("#taxi-phone").mask("+7 (999) 999-9999");

    /**
     * Дата и время
     */
    initPickers(true);
</script>