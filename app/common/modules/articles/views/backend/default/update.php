<?php
/**
 * Редактировать статью
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\articles\models\backend\ArticleForm
 * @var $model common\modules\articles\models\Article
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактировать статью';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление статьями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все статьи',
            'url' => ['/articles/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/articles/default/update', 'id' => $model->article_id],
        ]
    ]
]);


echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/articles/default/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/articles/default/delete', 'id' => $model->article_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
        'formModel' => $formModel,
        'model' => $model,
    ]);
