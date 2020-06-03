<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\realty\models\RentType */

$this->title = 'Редактировать тип аренды';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление типами аренды',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все типы аренды',
            'url' => ['/apartments/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/apartments/default/update', 'id' => $model->rent_type_id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/apartments/default/create'],
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