<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\partners\models\search\ReservationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => Yii::$app->request->get('city_eng') ? ['total-bid', 'city_eng' => Yii::$app->request->get('city_eng')] : Url::toRoute(['total-bid'], true),
    'method' => 'get',
    'id' => 'select-city-change-form-action'
]); ?>

<div class="reservation-filter clearfix">

    <?= $form->field($model, 'city_name')->textInput([
        'maxlength' => true,
        'class' => 'form-control city-drop-down-list',
        'data-url' => Url::to(['/geo/ajax/select-city'])
    ]) ?>

    <?= $form->field($model, 'rent_type')->dropDownList($model->rentTypesList, ['prompt' => 'Все']) ?>

    <?= $form->field($model, 'budget')->dropDownList($model->budgetList, ['prompt' => 'Все']) ?>

    <?= $form->field($model, 'currency')->dropDownList($model->currencyArray, ['prompt' => 'Любая']) ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
