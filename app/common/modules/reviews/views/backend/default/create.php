<?php
/**
 * Создать отзыв
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\reviews\models\ReviewForm
 * @var $model common\modules\reviews\models\Review
 */

use yii\helpers\Html;

$this->title = 'Создать отзыв';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление отзывами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все статьи',
            'url' => ['/reviews/default/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/reviews/default/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
