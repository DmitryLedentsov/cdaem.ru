<?php

/**
 * Редактировать страницу
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\pages\models\backend\PageForm
 * @var $model common\modules\pages\models\Page
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактировать страницу';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление статическими страницами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все страницы',
            'url' => ['/pages/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/pages/default/update', 'id' => $model->page_id],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/pages/default/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/pages/default/delete', 'id' => $model->page_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
]);
