<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\articles\models\Article */
/* @var $searchModel common\modules\articles\models\backend\ArticleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-md-2 col-md-2 col-sm-2 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'article_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($searchModel, 'title')->textInput(['maxlength' => true]) ?></div>
</div>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Поиcк'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
