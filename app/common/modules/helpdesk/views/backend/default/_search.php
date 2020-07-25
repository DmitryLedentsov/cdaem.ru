<?php

use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\helpdesk\models\backend\HelpdeskSearch */
/* @var $model common\modules\helpdesk\models\Helpdesk */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">

        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($searchModel, 'ticket_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($searchModel, 'user_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($searchModel, 'email') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($searchModel, 'theme') ?></div>

        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'priority')->dropDownList(ArrayHelper::getColumn($model->priorityArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'answered')->dropDownList(ArrayHelper::getColumn($model->answeredArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'department')->dropDownList(ArrayHelper::getColumn($model->departmentArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'source_type')->dropDownList(ArrayHelper::getColumn($model->sourceTypeArray, 'label'), ['prompt' => 'Все']) ?></div>

        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($searchModel, 'date_create')->widget(DatePicker::className(), [
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
        <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>