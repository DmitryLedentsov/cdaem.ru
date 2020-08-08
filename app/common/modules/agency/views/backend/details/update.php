<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\DetailsHistory */
/* @var $formModel common\modules\agency\models\backend\form\DetailsHistoryForm */


$this->title = 'Редактировать заявку на отправку реквизитов';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявки',
            'url' => ['/agency/details/index'],
        ],
        [
            'label' => 'Просмотр',
            'url' => ['/agency/details/update', 'id' => $model->id],
        ],
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/details/delete', 'id' => $model->id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
