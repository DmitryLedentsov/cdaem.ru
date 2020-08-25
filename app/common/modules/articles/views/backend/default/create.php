<?php
/**
 * Создать статью
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\articles\models\backend\ArticleForm
 * @var $model common\modules\articles\models\Article
 */

use yii\helpers\Html;

$this->title = 'Создать статью';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление статьями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все статьи',
            'url' => ['/articles/default/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/articles/default/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
]);
