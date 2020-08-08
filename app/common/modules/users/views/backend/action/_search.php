<?php
/**
 * Поиск действий пользователей
 * @var $this yii\web\View
 * @var $model common\modules\users\models\backend\search\ActionSearch
 */

use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<?php $form = ActiveForm::begin([
    'id' => 'search-model',
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'application') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'module') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'action') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'username') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'ip') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
            <?= $form->field($model, 'date_from')->widget(DatePicker::class, [
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
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
            <?= $form->field($model, 'date_to')->widget(DatePicker::class, [
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
        <?= Html::submitButton(Yii::t('users', 'SEARCH'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>