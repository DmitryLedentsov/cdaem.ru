<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Apartment */
/* @var $formModel common\modules\agency\models\backend\form\ApartmentForm */

$this->title = 'Создать апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/agency/default/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
