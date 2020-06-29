<?php
/**
 * Редактировать сео спецификации
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\seo\models\backend\SeoSpecificationForm
 * @var $model common\modules\seo\models\SeoSpecification
 */

$this->title = 'Редактировать сео спецификации';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление сео спецификациями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео спецификации',
            'url' => ['/seo/specification/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/seo/specification/update', 'id' => $model->id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
        'control' => [
            [
                'url' => ['/seo/specification/create'],
                'label' => 'Создать',
            ],
            [
                'url' => ['/seo/specification/delete', 'id' => $model->id],
                'label' => 'Удалить',
            ]
        ]
    ]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);