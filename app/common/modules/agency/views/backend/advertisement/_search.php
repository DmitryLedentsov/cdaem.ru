<?php

use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\backend\search\AdvertisementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'advert.apartment.apartment_id')->label('№ Апартаментов') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'advert_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'text') ?></div>


        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'date_expire')->widget(DatePicker::className(), [
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
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
