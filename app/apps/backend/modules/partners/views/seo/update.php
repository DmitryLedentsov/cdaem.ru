<?php
/**
 * Редактировать сео спецификации
 * @var yii\base\View $this Представление
 * @var $formModel backend\modules\partners\models\SeoSpecificationForm
 * @var $model common\modules\partners\models\SeoSpecification
 */

$this->title = 'Редактировать сео спецификации';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление сео спецификациями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео спецификации',
            'url' => ['/partners/seo/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/partners/seo/update', 'id' => $model->id],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/seo/create'],
            'label' => 'Создать',
        ],
        [
            'url' => ['/partners/seo/delete', 'id' => $model->id],
            'label' => 'Удалить',
        ]
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'formModel' => $formModel,
]);