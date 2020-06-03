<?php
/**
 * Создать сеотекст
 * @var yii\base\View $this Представление
 * @var $model common\modules\seo\models\Seotext
 */

use yii\helpers\Html;

$this->title = 'Создать сео-текст';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление сео-текстами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео-тексты',
            'url' => ['/seo/text/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/seo/text/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
]);
