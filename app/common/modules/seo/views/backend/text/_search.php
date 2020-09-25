<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\seo\models\backend\SeotextSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-2"><?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($model, 'type')->dropDownList($model->typeArray, ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($model, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>