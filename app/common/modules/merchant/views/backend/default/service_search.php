<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\merchant\models\backend\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['service'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-md-2 col-md-2 col-sm-2 col-xs-6 col-lg-1"><?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-2 col-md-2 col-sm-2 col-xs-6 col-lg-1"><?= $form->field($model, 'payment')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'service')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList(Yii::$app->getModule('merchant')->systems['partners']), ['prompt' => 'Все']) ?></div>
</div>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Поиcк'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
