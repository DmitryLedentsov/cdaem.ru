<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\Apartments */

$this->title = 'Создать апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/partners/default/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/partners/default/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
]);