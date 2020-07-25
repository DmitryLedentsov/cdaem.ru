<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $formModel common\modules\helpdesk\models\backend\HelpdeskForm */
/* @var $model common\modules\helpdesk\models\Helpdesk */
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

    <h6 class="heading-hr"><span class="icon-user"></span> Пользователь</h6>
    <div class="row">
        <?php if (!$formModel->scenario == 'update') : ?>
            <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-4"><?= $form->field($formModel, 'email')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'user_name')->textInput(['maxlength' => true]) ?></div>
        <?php else: ?>
            <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'user_id')->textInput(['maxlength' => true]) ?></div>
        <?php endif; ?>

        <?php if ($formModel->scenario == 'update') : ?>
            <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'date_create')->textInput(['disabled' => true]) ?></div>
            <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'date_close')->textInput(['disabled' => true]) ?></div>
        <?php endif; ?>
    </div>

    <h6 class="heading-hr"><i class="icon-bubble-notification2"></i> Основное</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'priority')->dropDownList(ArrayHelper::getColumn($model->priorityArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'source_type')->dropDownList(ArrayHelper::getColumn($model->sourceTypeArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'department')->dropDownList(ArrayHelper::getColumn($model->departmentArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'answered')->dropDownList(ArrayHelper::getColumn($model->answeredArray, 'label')) ?></div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-6"><?= $form->field($formModel, 'theme')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-8 col-md-68 col-sm-8 col-xs-12 col-lg-8"><?= $form->field($formModel, 'text')->textarea() ?></div>
    </div>

    <br/>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <br/>

<?php ActiveForm::end(); ?>