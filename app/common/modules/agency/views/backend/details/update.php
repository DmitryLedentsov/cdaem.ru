<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\DetailsHistory */
/* @var $formModel common\modules\agency\models\backend\form\DetailsHistoryForm */


$this->title = 'Редактировать заявку на отправку реквизитов';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Агенство',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Все заявки на реквизиты',
            'url' => ['/agency/details/index'],
        ],
        [
            'label' => 'Просмотр',
            'url' => ['/agency/details/update', 'id' => $model->id],
        ],
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
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
