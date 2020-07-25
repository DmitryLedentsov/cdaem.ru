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

    <br/>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
            <?= $form->field($formModel, 'city')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-4">
            <?php
            $url = '';
            if ($formModel->scenario == 'update') {
                if ($formModel->city) {
                    $url = '';
                    $url = Yii::$app->params['siteDomain'];
                    if ($formModel->city) {
                        $url = str_replace('<city>', $formModel->city, Yii::$app->params['siteSubDomain']);
                    }
                    $url .= '/stati/' . $formModel->slug;
                    $url = Html::a($url, $url);
                } else {
                    $url = Html::a(Yii::$app->params['siteDomain'] . '/stati/' . $formModel->slug, Yii::$app->params['siteDomain'] . '/stati/' . $formModel->slug);
                }
            }
            echo $form->field($formModel, 'slug', ['template' => "{label}\n{input} " . $url . "\n{hint}\n{error}"])->textInput(['maxlength' => true]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'title_img')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'file')->fileInput(['class' => 'styled']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'bgfile')->fileInput(['class' => 'styled']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'status')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($model->getStatusArray())) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
    </div>

<?php if (!$model->isNewRecord): ?>
    <div class="row">
        <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'date_create')->widget(DateTimePicker::className(), [
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
            <?= $form->field($formModel, 'short_text')->widget(\vova07\imperavi\Widget::className(), [
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
    <div class="row">
        <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <?= $form->field($formModel, 'full_text')->widget(\vova07\imperavi\Widget::className(), [
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

    <br/>

<?php if (Yii::$app->user->can('articles-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

    <br/>

<?php ActiveForm::end(); ?>