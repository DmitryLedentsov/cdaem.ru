<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\SpecialAdvert */
/* @var $formModel common\modules\agency\models\backend\form\SpecialAdvertForm */

$this->title = 'Создать специальное предложение';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление спецпредложениями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Агенство',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Все спецпредложения',
            'url' => ['/agency/specials/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/agency/specials/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
