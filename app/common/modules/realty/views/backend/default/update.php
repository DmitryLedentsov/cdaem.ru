<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\realty\models\RentType */

$this->title = 'Редактировать тип аренды';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление типами аренды',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все типы аренды',
            'url' => ['/realty/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/realty/default/update', 'id' => $model->rent_type_id],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/realty/default/create'],
            'label' => 'Создать',
        ],
        /*
        [
            'url' => ['/apartments/default/delete', 'id' => $model->rent_type_id],
            'label' => 'Удалить',
        ]*/
    ]
]);

echo $this->render('_form', [
    'model' => $model,
]);
