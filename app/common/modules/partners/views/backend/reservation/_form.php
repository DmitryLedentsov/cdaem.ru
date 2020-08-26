<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\Reservation */
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

<br/>


<?php $form = ActiveForm::begin(); ?>


<h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>


<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'user_id')->textInput(['disabled' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'children')->dropDownList($model->childrenArray) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'pets')->dropDownList($model->petsArray) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'clients_count')->dropDownList($model->clientsCountArray) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_arrived')->widget(DateTimePicker::class, [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]); ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_out')->widget(DateTimePicker::class, [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
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

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_actuality')->widget(DateTimePicker::class, [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]); ?>
    </div>

</div>

<div class="row">

    <div class="col-md-6 col-md-6 col-sm-6 col-xs-6 col-lg-6">
        <?= $form->field($model, 'more_info')->textArea() ?>
    </div>

</div>


<h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>


<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'city_id')->textInput([
            'class' => 'form-control select-city'
        ]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'rent_type')->dropDownList($model->rentTypesList, ['prompt' => 'Указано в объявлении']) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'rooms')->dropDownList($model->roomsList, ['prompt' => 'Указано в объявлении']) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'beds')->dropDownList($model->bedsList, ['prompt' => 'Указано в объявлении']) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'floor')->dropDownList($model->floorArray, ['prompt' => 'Указано в объявлении']) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'metro_walk')->dropDownList($model->metroWalkList, ['prompt' => 'Указано в объявлении']) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <div class="clearfix">
            <?php
            $budget = $form->field($model, 'budget', ['inputOptions' => [
                //'style' => 'width:auto;float:left',
                //'style' => 'width:auto;float:left',
                'class' => 'form-control'
            ]]);
            $budget->parts['{input}'] = Html::activeTextInput($model, 'money_from', $budget->inputOptions);
            $budget->parts['{input}'] .= Html::activeTextInput($model, 'money_to', $budget->inputOptions);
            $budget->parts['{input}'] .= Html::activeDropDownList($model, 'currency', $model->currencyArray, array_merge($budget->inputOptions, ['prompt' => 'Нету']));

            echo $budget;
            ?>
        </div>
    </div>

</div>


<h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>


<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'closed')->dropDownList(Yii::$app->formatter->booleanFormat) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'cancel')->dropDownList($model->cancelList) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-6 col-md-6 col-sm-6 col-xs-6 col-lg-6">
        <?= $form->field($model, 'cancel_reason')->textArea() ?>
    </div>

</div>


<br/>


<?php if (Yii::$app->user->can('partners-reservation-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>


<?php ActiveForm::end(); ?>
