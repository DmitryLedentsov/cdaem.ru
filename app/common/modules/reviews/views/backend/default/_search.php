<?php

/* @var $this yii\web\View */
/* @var $model common\modules\reviews\models\Review */
/* @var $searchModel common\modules\reviews\models\ReviewSearch */

/* @var $form yii\widgets\ActiveForm */

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($searchModel, 'apartment_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-1"><?= $form->field($searchModel, 'user_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($searchModel, 'match_description')->dropDownList(ArrayHelper::getColumn($model->ratingMatchDescriptionArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($searchModel, 'price_quality')->dropDownList(ArrayHelper::getColumn($model->ratingPriceAndQualityArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($searchModel, 'cleanliness')->dropDownList(ArrayHelper::getColumn($model->ratingCleanlinessArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($searchModel, 'entry')->dropDownList(ArrayHelper::getColumn($model->ratingEntryArray, 'label'), ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-1"><?= $form->field($searchModel, 'moderation')->dropDownList(Yii::$app->formatter->booleanFormat, ['prompt' => 'Все']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($searchModel, 'date_create')->widget(DatePicker::class, [
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