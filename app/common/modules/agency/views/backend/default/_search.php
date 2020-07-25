<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\backend\search\ApartmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<?php
/*
    <?php $form->field($model, 'apartment') ?>

    <?php // echo $form->field($model, 'district1') ?>

    <?php // echo $form->field($model, 'district2') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'total_rooms') ?>

    <?php // echo $form->field($model, 'total_area') ?>

    <?php $form->field($model, 'visible') ?>

    <?php // echo $form->field($model, 'remont') ?>

    <?php // echo $form->field($model, 'metro_walk') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'date_create') ?>

    <?php // echo $form->field($model, 'date_update') ?>
 */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'apartment_id')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'apartment')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($model, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($model, 'adverts.rent_type')->dropDownList($model->rentTypesList, ['prompt' => 'Все']) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>