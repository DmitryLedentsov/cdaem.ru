<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advert */
/* @var $formModel common\modules\agency\models\backend\form\AdvertForm */

$this->title = 'Создать объявление';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Агенство',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Все апартаменты',
            'url' => ['/agency/default/index'],
        ],

        [
            'label' => $apartment->address,
            'url' => ['/agency/default/update', 'id' => $apartment->apartment_id],
        ],

        [
            'label' => 'Создать объявление',
            'url' => ['/agency/adverts/create', 'id' => $model->apartment_id],
        ]
    ]
]);


echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
