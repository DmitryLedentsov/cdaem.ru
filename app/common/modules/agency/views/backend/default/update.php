<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Apartment */
/* @var $formModel common\modules\agency\models\backend\form\ApartmentForm */
/* @var $advertsDataProvider yii\data\ActiveDataProvider */

$this->title = 'Редактировать апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/agency/default/update', 'id' => $model->apartment_id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/default/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/agency/default/delete', 'id' => $model->apartment_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
    'advertsDataProvider' => $advertsDataProvider,
]);
