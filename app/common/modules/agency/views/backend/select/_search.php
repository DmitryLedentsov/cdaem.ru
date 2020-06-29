<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\backend\search\SelectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'apartment_select_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'name') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'phone') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'email') ?></div>

        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'rooms')->dropDownList($model->roomsList, ['prompt' => 'Все']) ?></div>
        <div class="col-md-4 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'description') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'status')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($model->getStatusesArray()), ['prompt' => 'Все'])  ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
    </div>

    <br />

<?php ActiveForm::end(); ?>