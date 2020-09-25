<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $formModel common\modules\pages\models\backend\PageForm */
/* @var $model common\modules\pages\models\Page */
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

    <br/>

<?php $form = ActiveForm::begin(); ?>

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>
    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-3">
            <?php
            $url = ($model->scenario == 'update') ? '' : Html::a(Yii::$app->params['siteDomain'] . '/page/' . $formModel->url, Yii::$app->params['siteDomain'] . '/page/' . $formModel->url);
            echo $form->field($formModel, 'url', ['template' => "{label}\n{input} " . $url . "\n{hint}\n{error}"])->textInput(['maxlength' => true]);
            ?>
        </div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'status')->dropDownList(ArrayHelper::getColumn($model->statusArray, 'label')) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'active')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
    </div>

    <h6 class="heading-hr"><span class="icon-tags"></span> Мета теги</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-4"><?= $form->field($formModel, 'title')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-4"><?= $form->field($formModel, 'description')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-4"><?= $form->field($formModel, 'keywords')->textInput(['maxlength' => true]) ?></div>
        </div>
    </div>

    <h6 class="heading-hr"><i class="icon-text-width"></i> Содержимое</h6>
    <div class="row">
        <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <?= $form->field($formModel, 'text')->widget(\vova07\imperavi\Widget::class, [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 200,
                    'imageUpload' => Url::to(['/pages/default/image-upload']),
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

    <br/>

<?php if (Yii::$app->user->can('pages-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

    <br/>


<?php ActiveForm::end(); ?>