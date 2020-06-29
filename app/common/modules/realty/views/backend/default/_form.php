<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\url;

/* @var $this yii\web\View */
/* @var $model common\modules\realty\models\RentType */
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

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>
    <div class="row">

        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'sort')->textInput() ?></div>
    </div>

    <h6 class="heading-hr"><span class="icon-tags"></span> Мета теги</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-4"><?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-4"><?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-4"><?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?></div>
        </div>
    </div>


    <h6 class="heading-hr"><span class="icon-text-width"></span> Содержимое</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-8"><?= $form->field($model, 'short_description')->textarea(['rows' => 6]) ?></div>
        </div>
    </div>

    <h6 class="heading-hr"><span class="icon-text-width"></span> Агентство</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-8"><?= $form->field($model, 'agency_seo_short_desc')->textarea(['rows' => 6]) ?></div>
        </div>
        <div class="row">
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-8">
                <?= $form->field($model, 'agency_rules')->widget(\vova07\imperavi\Widget::className(), [
                    'settings' => [
                        'lang' => 'ru',
                        'minHeight' => 200,
                        'formattingAdd' => new \yii\web\JsExpression('[{title: "Очистить форматирование",  func: "inline.removeFormat"}]'),
                        'buttons' => [
                            'html', 'formatting', 'bold', 'italic', 'deleted', 'underline',
                            'unorderedlist', 'orderedlist', 'outdent', 'indent',
                            'image', 'link', 'alignment', 'horizontalrule'
                        ],
                        'plugins' => [
                            'fontcolor',
                            'fontsize',
                            'fontfamily',
                            'table',
                            'fullscreen',
                            'imagemanager'
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

    <h6 class="heading-hr"><span class="icon-text-width"></span> Дополнительно</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-8"><?= $form->field($model, 'icons')->textarea(['rows' => 6]) ?></div>
        </div>
    </div>



    <br />

    <?php if (Yii::$app->user->can('realty-rent-type-update')) : ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>

    <br />

<?php ActiveForm::end(); ?>

