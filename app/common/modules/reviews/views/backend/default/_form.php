<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $formModel common\modules\reviews\models\ReviewForm */
/* @var $model common\modules\reviews\models\Review */
?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success indent-bottom">
        <span class="icon-checkmark-circle"></span> <?php echo Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('danger')): ?>
    <div class="alert alert-danger indent-bottom">
        <span class="icon-cancel-circle"></span> <?php echo Yii::$app->session->getFlash('danger') ?>
    </div>
<?php endif; ?>

    <br/>

<?php $form = ActiveForm::begin(); ?>

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'apartment_id') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'user_id') ?></div>
    </div>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'match_description')->dropDownList(ArrayHelper::getColumn($model->ratingMatchDescriptionArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'price_quality')->dropDownList(ArrayHelper::getColumn($model->ratingPriceAndQualityArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'cleanliness')->dropDownList(ArrayHelper::getColumn($model->ratingCleanlinessArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'entry')->dropDownList(ArrayHelper::getColumn($model->ratingEntryArray, 'label')) ?></div>
    </div>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'moderation')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
    </div>

<?php if ($formModel->scenario == 'update'): ?>
    <div class="row">
        <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'date_create')->widget(DateTimePicker::class, [
                'type' => DateTimePicker::TYPE_INPUT,
                'options' => [
                    'readonly' => 'readonly',
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:00',
                    'todayHighlight' => true,
                    'todayBtn' => true,
                ]
            ]);
            ?>
        </div>
    </div>
<?php endif; ?>

    <h6 class="heading-hr"><i class="icon-text-width"></i> Содержимое</h6>
    <div class="row">
        <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-10">
            <?= $form->field($formModel, 'text')->textarea() ?>
        </div>
    </div>

    <br/>

<?php if (Yii::$app->user->can('reviews-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

    <br/>

<?php ActiveForm::end(); ?>