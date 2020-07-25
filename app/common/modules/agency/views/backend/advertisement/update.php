<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advertisement */
/* @var $formModel common\modules\agency\models\backend\form\AdvertisementForm */

$this->title = 'Редактировать объявление';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление рекламными объявлениями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все рекламные объявления',
            'url' => ['/agency/advertisement/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/agency/advertisement/update', 'id' => $model->advertisement_id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/advertisement/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/agency/advertisement/delete', 'id' => $model->advertisement_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);