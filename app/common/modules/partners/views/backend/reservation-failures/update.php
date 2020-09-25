<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\ReservationFailure */

$this->title = 'Редактировать апартаменты';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками "Незаезд"',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все "Незаезд" заявки',
            'url' => ['/partners/reservation-failures/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/partners/reservation-failures/update', 'id' => $model->id],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/reservation-failures/delete', 'id' => $model->id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
]);
