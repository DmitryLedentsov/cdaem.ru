<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Select */
/* @var $formModel common\modules\agency\models\backend\form\SelectForm */

$this->title = 'Редактировать заявку на "Быстро подберём квартиру"';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявками',
            'url' => ['/agency/select/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/agency/select/update', 'id' => $model->apartment_select_id],
        ],
    ]
]);


echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/select/delete', 'id' => $model->apartment_select_id],
            'label' => 'Удалить',
        ],
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
