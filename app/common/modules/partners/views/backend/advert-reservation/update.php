<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\AdvertReservation */

$this->title = 'Редактировать апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление бронями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все брони',
            'url' => ['/partners/advert-reservation/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/partners/advert-reservation/update', 'id' => $model->id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/advert-reservation/delete', 'id' => $model->id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
]);
