<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\ApartmentAdverts */


echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/partners/default/index'],
        ],

        [
            'label' => $model->apartment->address,
            'url' => ['/partners/default/update', 'id' => $model->apartment->apartment_id],
        ],

        [
            'label' => 'Создать объявление',
            'url' => ['/partners/adverts/create', 'id' => $model->apartment_id],
        ]
    ]
]);


echo $this->render('_form', [
    'model' => $model,
]);
