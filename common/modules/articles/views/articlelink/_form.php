<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $formModel common\modules\articles\models\backend\ArticleForm */
/* @var $model common\modules\articles\models\Article */
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

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'title')->textInput(['maxlength' => true]) ?></div>
        
       <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'link_page')->textInput(['maxlength' => true]) ?></div>
       <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'article_id')->dropDownList($model->articlesList, ['prompt' => '']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'file')->fileInput(['class' => 'styled']) ?></div>
    </div>

    <h6 class="heading-hr"><i class="icon-text-width"></i> Содержимое</h6>
 
    <div class="row">
        <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <?= $form->field($formModel, 'text')->widget(\vova07\imperavi\Widget::className(), [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 200,
                    'imageUpload' => Url::to(['/articles/default/image-upload']),
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

    <br />

    <?php if (Yii::$app->user->can('articles-update')) : ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>

    <br />

<?php ActiveForm::end(); ?>