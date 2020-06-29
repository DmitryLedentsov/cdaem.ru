<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\merchant\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-2 col-md-2 col-sm-2 col-xs-6 col-lg-1"><?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'type')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($model->getTypeArray()), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'system')->dropDownList($model->getSystems(), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'date')->widget(DatePicker::className(), [
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
