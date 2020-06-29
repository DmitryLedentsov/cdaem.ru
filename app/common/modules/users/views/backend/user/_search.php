<?php
/**
 * Поиск пользователей
 * @var $this yii\web\View
 * @var $model common\modules\users\models\backend\search\UserSearch
*/

use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>


<?php $form = ActiveForm::begin([
    'id' => 'search-model',
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'group')->dropDownList(\nepster\users\rbac\models\AuthItem::getGroupsArray(), ['prompt' => '']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'user') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'contacts') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'profile.user_type')->dropDownList(ArrayHelper::getColumn($model->userTypeArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'profile.user_partner')->dropDownList(ArrayHelper::getColumn($model->userPartnerArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'banned')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => '']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($model, 'status')->dropDownList($model->statusArray, ['prompt' => '']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
            <?= $form->field($model, 'date_from')->widget(DatePicker::className(), [
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
            <?= $form->field($model, 'date_to')->widget(DatePicker::className(), [
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