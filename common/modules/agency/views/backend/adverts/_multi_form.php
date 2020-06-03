<?php
/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advert */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
?>


<h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']price');
            echo Html::activeTextInput($model, '['.$model->advert_id.']price', ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']price', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']currency');
            echo Html::activeDropDownList($model, '['.$model->advert_id.']currency', $model->currencyList, ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']currency', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']main_page');
            echo Html::activeDropDownList($model, '['.$model->advert_id.']main_page', Yii::$app->formatter->booleanFormat, ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']main_page', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
</div>


<h6 class="heading-hr"><span class="icon-tags"></span> Мета теги</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']meta_title');
            echo Html::activeTextInput($model, '['.$model->advert_id.']meta_title', ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']meta_title', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-4">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']meta_description');
            echo Html::activeTextInput($model, '['.$model->advert_id.']meta_description', ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']meta_description', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-4">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']meta_keywords');
            echo Html::activeTextInput($model, '['.$model->advert_id.']meta_keywords', ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']meta_keywords', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
</div>


<h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?php
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::activeLabel($model, '['.$model->advert_id.']text', ['label' => 'Описание для правого блока с типами аренды (например для акции)']);
            echo Html::activeTextarea($model, '['.$model->advert_id.']text', ['class' => 'form-control']);
            echo Html::error($model, '['.$model->advert_id.']text', ['class' => 'help-block']);
            echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?php
        echo Html::beginTag('div', ['class' => 'form-group']);
        echo Html::activeLabel($model, '['.$model->advert_id.']info', ['label' => 'Дополнительное описание']);
        echo \vova07\imperavi\Widget::widget([
            'model' => $model,
            'options' => [
                'data' => [
                    'redactor' => true
                ],
            ],
            'attribute' => '['.$model->advert_id.']info',
            'settings' => [
                'changeCallback' =>  new JsExpression('function(){imperaviChangeCallback(this)}'),
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/agency/default/image-upload']),
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
        echo Html::error($model, 'info', ['class' => 'help-block']);
        echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?php
        echo Html::beginTag('div', ['class' => 'form-group']);
        echo Html::activeLabel($model, '['.$model->advert_id.']rules', ['label' => 'Правила заселения для типа аренды']);
        echo \vova07\imperavi\Widget::widget([
            'model' => $model,
            'options' => [
                'data' => [
                    'redactor' => true
                ],
            ],
            'attribute' => '['.$model->advert_id.']rules',
            'settings' => [
                'changeCallback' =>  new JsExpression('function(){imperaviChangeCallback(this)}'),
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/agency/default/image-upload']),
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
        echo Html::error($model, 'rules', ['class' => 'help-block']);
        echo Html::endTag('div');
        ?>
    </div>
</div>