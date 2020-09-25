<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\WantPass */
/* @var $formModel common\modules\agency\models\backend\form\WantPassForm */


$this->title = 'Редактировать заявку на "Хочу сдать"';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Агенство',
            'url' => ['/agency/default/index'],
        ],
        [
            'label' => 'Хочу сдать квартиру',
            'url' => null,
        ],
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

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
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
