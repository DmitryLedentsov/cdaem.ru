<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\Apartments */

$this->title = 'Редактировать апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/partners/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/partners/default/update', 'id' => $model->apartment_id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/default/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/partners/default/delete', 'id' => $model->apartment_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'advertsDataProvider' => $advertsDataProvider,
]);
