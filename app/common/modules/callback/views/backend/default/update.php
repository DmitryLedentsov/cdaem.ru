<?php
/**
 * Редактировать заявку на обратный звонок
 * @var yii\base\View $this Представление
 * @var $formModel common\modules\callback\models\backend\CallbackForm
 * @var $model common\modules\callback\models\Callback
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактировать заявку на обратный звонок';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Редактировать заявку на обратный звонок',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявки на обратный звонок',
            'url' => ['/callback/default/index'],
        ],
        [
            'label' => 'Редактировать',
            'url' => ['/callback/default/update', 'id' => $model->callback_id],
        ]
    ]
]);
?>


<div class="callback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'formModel' => $formModel,
        'model' => $model,
    ]) ?>

</div>
