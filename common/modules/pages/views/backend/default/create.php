<?php
/**
 * Создать страницу
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\pages\models\backend\PageForm
 * @var $model common\modules\pages\models\Page
 */

use yii\helpers\Html;

$this->title = 'Создать страницу';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление статическими страницами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все страницы',
            'url' => ['/pages/default/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/pages/default/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
    ]);
