<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\modules\seo\models\Seotext */
/* @var $form yii\widgets\ActiveForm */
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

    <br />


<?php $form = ActiveForm::begin(); ?>

    <h4 class="heading-hr">Размещение сео-текстов на страницах сайта.</h4>

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>

    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-2">
            <?php
                $url = $model->isNewRecord ? '' : Html::a(Yii::$app->params['siteDomain'] . $model->url, Yii::$app->params['siteDomain'] . $model->url);
                echo $form->field($model, 'url', ['template' => "{label}\n{input} " . $url . "\n{hint}\n{error}"])->textInput(['maxlength' => true]);
            ?>
        </div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($model, 'type')->dropDownList($model->typeArray) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($model, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
    </div>

    <h6 class="heading-hr"><i class="icon-text-width"></i> Текст</h6>
    <div class="row">
        <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-10">
            <?= $form->field($model, 'text')->textarea()?>
        </div>
    </div>

    <br />

    <?php if (Yii::$app->user->can('seotext-update')) : ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>

    <br />

<?php ActiveForm::end(); ?>