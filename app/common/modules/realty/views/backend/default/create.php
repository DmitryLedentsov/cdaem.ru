<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\realty\models\RentType */

$this->title = 'Создать тип аренды';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление типами аренды',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все типы аренды',
            'url' => ['/realty/default/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/realty/default/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
]);
