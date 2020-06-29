<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\AdvertReservationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'city_id')->textInput(['maxlength' => true, 'class' => 'form-control select-city']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'landlord_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'advert_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'actuality')->dropDownList([1 => 'Только актуальные', 0 => 'Неактуальные'], ['prompt' => 'Все'])->label('Актуальность') ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'rent_type')->dropDownList($model->rentTypesList, ['prompt' => 'Все']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'cancel')->dropDownList([1 => 'Да', 0 => 'Нет'], ['prompt' => 'Все'])->label('Отмененные') ?></div>
</div>

<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'confirm')->dropDownList($model->confirmList, ['prompt' => 'Все']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'closed')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все'])?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'failed')->dropDownList([1 => 'Только с "Незаездом"'], ['prompt' => 'Все'])?></div>
</div>

<div class="form-group">
    <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
