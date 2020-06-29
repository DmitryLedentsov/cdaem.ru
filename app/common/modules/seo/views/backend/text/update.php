<?php
/**
 * Редактировать сео-текс
 * @var yii\base\View $this Представление
 * @var $model common\modules\seo\models\Seotext
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактировать сео-текст';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление сео-текстами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео-тексты',
            'url' => ['/seo/text/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/seo/text/update', 'id' => $model->text_id],
        ]
    ]
]);


echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/seo/text/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/seo/text/delete', 'id' => $model->text_id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
        'model' => $model,
    ]);
