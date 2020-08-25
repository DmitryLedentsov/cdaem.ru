<?php
/**
 * Создать сео спецификации
 * @var yii\base\View $this Представление
 * @var $formModel backend\modules\partners\models\SeoSpecificationForm
 * @var $model common\modules\partners\models\SeoSpecification
 */

$this->title = 'Создать сео спецификации';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление сео спецификациями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео спецификации',
            'url' => ['/partners/seo/index'],
        ],
        [
            'label' => 'Создать',
            'url' => ['/partners/seo/create'],
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);
