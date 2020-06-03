<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\ReservationFailure */
/* @var $form yii\widgets\ActiveForm */
?>


<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success indent-bottom">
        <span class="icon-checkmark-circle"></span> <?php echo Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('danger')): ?>
    <div class="alert alert-danger indent-bottom">
        <span class="icon-cancel-circle"></span> <?php echo Yii::$app->session->getFlash('danger') ?>
    </div>
<?php endif; ?>

<br />


<?php $form = ActiveForm::begin(); ?>


<h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>


<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'user_id')->textInput(['disabled' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'reservation_id')->textInput(['disabled' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'processed')->textInput(['disabled' => true, 'value' => Yii::$app->formatter->asBoolean($model->processed)]) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_to_process')->widget(DateTimePicker::className(), [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]); ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'date_create')->textInput(['disabled' => true]) ?>
    </div>

</div>


<div class="row">

    <div class="col-md-6 col-md-6 col-sm-6 col-xs-6 col-lg-6">
        <?= $form->field($model, 'cause_text')->textArea() ?>
    </div>

</div>

<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'closed')->dropDownList(Yii::$app->formatter->booleanFormat)->label('Автоматический возврат сердств выключен') ?>
    </div>

</div>

<br />


<?php if (Yii::$app->user->can('partners-reservation-failure-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>


<?php ActiveForm::end(); ?>
