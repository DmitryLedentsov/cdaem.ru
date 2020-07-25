<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Reservation */
/* @var $formModel common\modules\agency\models\backend\form\ReservationForm */

$this->title = 'Редактировать бронь';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление бронями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все брони',
            'url' => ['/agency/reservation/index'],
        ],
        [
            'label' => 'Редактировать' . ' ID - ' . $model->reservation_id,
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/reservation/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/agency/reservation/delete', 'id' => $model->reservation_id],
            'label' => 'Удалить',
        ]
    ]
]);
?>

<?= $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
]) ?>
