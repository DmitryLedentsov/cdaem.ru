<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\LogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    //'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'level')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'category')->dropDownList($model->getCategoryArray(), ['prompt' => 'Все']) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'log_time')->widget(DatePicker::class, [
            'type' => DatePicker::TYPE_INPUT,
            'options' => [
                'readonly' => 'readonly',
            ],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true
            ]
        ]);
        ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Поиcк'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
