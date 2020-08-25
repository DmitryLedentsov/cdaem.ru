<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $formModel common\modules\helpdesk\models\backend\HelpdeskForm */
/* @var $model common\modules\helpdesk\models\Helpdesk */

$this->title = 'Редактировать тикет';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Техническая поддержка',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все тикеты',
            'url' => ['/helpdesk/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/helpdesk/default/update', 'id' => $formModel->ticket_id],
        ]
    ]
]);


echo $this->render('_form', [
    'formModel' => $formModel,
    'model' => $model,
]);
