<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\DetailsHistory */
/* @var $formModel common\modules\agency\models\backend\form\DetailsHistoryForm */
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

    <h6 class="heading-hr"><span class="icon-user"></span> Контакты</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'phone')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'email')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'processed')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
    </div>

    <h6 class="heading-hr"><i class="icon-bubble-notification2"></i> Основное</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'type')->dropDownList($model->getTypeArray()) ?></div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'payment')->dropDownList($model->getPaymentArray()) ?></div>
    </div>

    <br/>

<?php if (Yii::$app->user->can('agency-details-history-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

    <br/>

<?php ActiveForm::end(); ?>