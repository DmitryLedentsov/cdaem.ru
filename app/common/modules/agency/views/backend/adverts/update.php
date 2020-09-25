<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Advert */
/* @var $formModel common\modules\agency\models\backend\form\AdvertForm */

$this->title = 'Редактировать объявление';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Агенство',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => $model->apartment->address,
            'url' => ['/agency/default/update', 'id' => $model->apartment_id],
        ],

        [
            'label' => 'Редактировать объявление',
            'url' => ['/agency/adverts/update', 'id' => $model->advert_id],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/adverts/create', 'id' => $model->apartment_id],
            'label' => 'Создать',
        ],
        [
            'url' => ['/agency/adverts/delete', 'id' => $model->advert_id],
            'label' => 'Удалить',
        ]
    ]
]);


echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
