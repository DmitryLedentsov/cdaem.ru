<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advertisement */
/* @var $formModel common\modules\agency\models\backend\form\AdvertisementForm */
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

<h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($formModel, 'advert_id')->textInput() ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'date_start')->widget(DateTimePicker::class, [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]);
        ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'date_expire')->widget(DateTimePicker::class, [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]);
        ?>
    </div>
</div>

<br/>

<h6 class="heading-hr"><i class="icon-text-width"></i> Содержимое</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-6 col-lg-6"><?= $form->field($formModel, 'text')->textarea() ?></div>
</div>

<br/>

<div class="form-group">
    <?= Html::submitButton(($formModel->scenario == 'create') ? 'Создать' : 'Редактировать', ['class' => ($model->scenario == 'create') ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
