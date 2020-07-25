<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\WantPass */
/* @var $formModel common\modules\agency\models\backend\form\WantPassForm */


$this->title = 'Редактировать заявку на "Хочу сдать"';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявками',
            'url' => ['/agency/want-pass/index'],
        ],
        [
            'label' => 'Просмотр',
            'url' => ['/agency/want-pass/update', 'id' => $model->apartment_want_pass_id],
        ],
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/want-pass/delete', 'id' => $model->apartment_want_pass_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);