<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\ApartmentAdverts */

$this->title = 'Редактировать объявление';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/partners/default/index'],
        ],

        [
            'label' => $model->apartment->address,
            'url' => ['/partners/default/update', 'id' => $model->apartment_id],
        ],

        [
            'label' => 'Редактировать объявление',
            'url' => ['/partners/adverts/update', 'id' => $model->advert_id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/adverts/create', 'id' => $model->apartment_id],
            'label' => 'Создать',
        ],
        [
            'url' => ['/partners/adverts/delete', 'id' => $model->advert_id],
            'label' => 'Удалить',
        ]
    ]
]);


echo $this->render('_form', [
    'model' => $model,
]);
