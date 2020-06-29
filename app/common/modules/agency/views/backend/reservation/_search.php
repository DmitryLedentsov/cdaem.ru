<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\agency\models\ApartmentReservationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'reservation_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'apartment_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'phone') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'email') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'processed')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>