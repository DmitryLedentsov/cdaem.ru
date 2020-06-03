<?php
/**
 * Редактировать отзыв
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\reviews\models\ReviewForm
 * @var $model common\modules\reviews\models\Review
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактировать отзыв';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление отзывами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все отзывы',
            'url' => ['/reviews/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/reviews/default/update', 'id' => $model->review_id],
        ]
    ]
]);


echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/reviews/default/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/reviews/default/delete', 'id' => $model->review_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
    ]);
