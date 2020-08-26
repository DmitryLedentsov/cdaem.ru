<?php
/**
 * Создать статью
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\articles\models\backend\ArticleForm
 * @var $model common\modules\articles\models\Article
 */

use yii\helpers\Html;

$this->title = 'Создать рекламу к статье';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление ссылками к статьям',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все записи',
            'url' => ['/articles/articlelink/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/articles/articlelink/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
]);
