<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\SpecialAdvert */
/* @var $formModel common\modules\agency\models\backend\form\SpecialAdvertForm */

$this->title = 'Редактировать специальное предложение';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление спецпредложениями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Создать',
            'url' => ['/agency/specials/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/agency/specials/update', 'id' => $model->special_id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'label' => 'Создать',
            'url' => ['/agency/specials/index'],
        ],
        [
            'label' => 'Удалить',
            'url' => ['/agency/specials/delete', 'id' => $model->special_id],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
