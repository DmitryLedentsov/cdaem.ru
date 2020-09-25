<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\ReservationFailureSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'reservation_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'processed')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'conflict')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($model->conflictArray), ['prompt' => 'Все']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'cause_text')->textInput(['maxlength' => true]) ?></div>
</div>

<div class="form-group">
    <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
