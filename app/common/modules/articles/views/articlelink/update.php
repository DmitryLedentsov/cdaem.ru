<?php
/**
 * Редактировать статью
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\articles\models\backend\ArticleForm
 * @var $model common\modules\articles\models\Article
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактировать рекламу';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление ссылками к статьям',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все статьи',
            'url' => ['/articles/articlelink/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/articles/articlelink/update', 'id' => $model->id],
        ]
    ]
]);


echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/articles/articlelink/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/articles/articlelink/delete', 'id' => $model->id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
]);
