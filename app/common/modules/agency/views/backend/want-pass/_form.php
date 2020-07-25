<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\WantPass */
/* @var $formModel common\modules\agency\models\backend\form\WantPassForm */
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


    <div class="row">
        <?php foreach ($model->images_array as $image) : ?>
            <div class="thumbnail thumbnail-boxed">
                <div class="thumb">
                    <a href="<?= $image ?>" class="thumb-zoom lightbox" title="">
                        <img src="<?= $image ?>" alt="">
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <br/>


<?php $form = ActiveForm::begin(); ?>

    <h6 class="heading-hr"><span class="icon-user"></span> Пользователь</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-4"><?= $form->field($formModel, 'phone')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'email')->textInput(['maxlength' => true]) ?></div>
    </div>

    <h6 class="heading-hr"><i class="icon-bubble-notification2"></i> Основное</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'rooms')->dropDownList($model->roomsList) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'status')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($model->getStatusesArray())) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'date_create')->textInput(['maxlength' => true, 'disabled' => true]) ?></div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-3"><?= $form->field($formModel, 'rent_types_array')->checkBoxList($model->rentTypesList) ?></div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-6"><?= $form->field($formModel, 'description')->textarea() ?></div>
    </div>

    <h6 class="heading-hr"><i class="icon-ladder"></i> Метро</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-8"><?= $form->field($formModel, 'metro_array')->checkBoxList($model->metroStations) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'address') ?></div>
    </div>

    <br/>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <br/>

<?php ActiveForm::end(); ?>


<?php
$this->registerCss('#wantpass-rent_types_array label {display: block}');
?>