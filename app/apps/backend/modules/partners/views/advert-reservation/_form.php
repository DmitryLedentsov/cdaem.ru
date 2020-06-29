<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\AdvertReservation */
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

<br />


<?php $form = ActiveForm::begin(); ?>


<h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>


<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'user_id')->textInput(['disabled' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'landlord_id')->textInput(['disabled' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'advert_id')->textInput(['disabled' => true]) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'children')->dropDownList($model->childrenArray) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'pets')->dropDownList($model->petsArray) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'clients_count')->dropDownList($model->clientsCountArray) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_arrived')->widget(DateTimePicker::className(), [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]); ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_out')->widget(DateTimePicker::className(), [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]); ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
        <?= $form->field($model, 'date_create')->textInput(['disabled' => true]) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'date_actuality')->widget(DateTimePicker::className(), [
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]);?>
    </div>

</div>

<div class="row">

    <div class="col-md-6 col-md-6 col-sm-6 col-xs-6 col-lg-6">
        <?= $form->field($model, 'more_info')->textArea() ?>
    </div>

</div>


<h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>


<div class="row">

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'closed')->dropDownList(Yii::$app->formatter->booleanFormat) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'cancel')->dropDownList($model->cancelList) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'confirm')->dropDownList($model->confirmList) ?>
    </div>

    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?= $form->field($model, 'landlord_open_contacts')->dropDownList(Yii::$app->formatter->booleanFormat) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-6 col-md-6 col-sm-6 col-xs-6 col-lg-6">
        <?= $form->field($model, 'cancel_reason')->textArea() ?>
    </div>

</div>


<br />


<?php if (Yii::$app->user->can('partners-advert-reservation-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>


<?php ActiveForm::end(); ?>
