<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advertisement */
/* @var $formModel common\modules\agency\models\backend\form\AdvertisementForm */

$this->title = 'Создать объявление';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление рекламными объявлениями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Агенство',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Все рекламные объявления',
            'url' => ['/agency/advertisement/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/agency/advertisement/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
