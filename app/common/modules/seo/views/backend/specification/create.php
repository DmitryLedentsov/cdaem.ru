<?php
/**
 * Создать сео спецификации
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\seo\models\backend\SeoSpecificationForm
 * @var $model common\modules\seo\models\SeoSpecification
 */

$this->title = 'Создать сео спецификации';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  'Управление сео спецификациями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео спецификации',
            'url' => ['/seo/specification/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/seo/specification/create'],
        ]
    ]
]);

echo $this->render('_form', [
        'model' => $model,
        'formModel' => $formModel,
    ]);
