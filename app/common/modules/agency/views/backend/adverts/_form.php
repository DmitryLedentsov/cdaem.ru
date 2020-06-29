<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advert */
/* @var $formModel common\modules\agency\models\backend\form\AdvertForm */

$rentTypeDisabled = $formModel->scenario == 'update' ? true : false;
$mainPageSelected = $formModel->scenario == 'create' ? true : false;
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

<h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'rent_type')->dropDownList($model->rentTypesList, ['disabled' => $rentTypeDisabled]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'price')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'currency')->dropDownList($model->currencyList) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'main_page')->dropDownList(Yii::$app->formatter->booleanFormat, ['options' => [1 => ['selected ' => $mainPageSelected]]]) ?></div>
</div>


<h6 class="heading-hr"><span class="icon-tags"></span> Мета теги</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-4"><?= $form->field($formModel, 'meta_title') ?></div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-4"><?= $form->field($formModel, 'meta_description') ?></div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-4"><?= $form->field($formModel, 'meta_keywords') ?></div>
</div>

<h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?= $form->field($formModel, 'text')->label('Описание для правого блока с типами аренды (например для акции)')->textArea() ?>
    </div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?= $form->field($formModel, 'info')->label('Дополнительное описание')->widget(\vova07\imperavi\Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/agency/default/image-upload']),
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
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?= $form->field($formModel, 'rules')->label('Правила заселения для типа аренды')->widget(\vova07\imperavi\Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/agency/default/image-upload']),
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


<?php if (Yii::$app->user->can('agency-advert-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

<br />

<?php ActiveForm::end(); ?>