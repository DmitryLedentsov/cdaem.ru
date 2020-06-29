<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $formModel common\modules\helpdesk\models\backend\AnswerForm */
/* @var $model common\modules\helpdesk\models\Helpdesk */

$this->title = 'Просмотр тикета';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Техническая поддержка',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все тикеты',
            'url' => ['/helpdesk/default/index'],
        ],
        [
            'label' => 'Просмотр',
            'url' => ['/helpdesk/default/view', 'id' => $model->ticket_id],
        ]
    ]
]);

echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ticket_id',
            [
                'label' => 'Пользователь',
                'format' => 'html',
                'value' => ($model->user) ? Html::a($model->user->profile->name . ' ' . $model->user->profile->surname, ['/users/user/update', 'id' => $model->user_id]) : $model->email,
            ],
            'theme:text',
            [
                'label' => 'Текст',
                'format' => 'html',
                'value' => nl2br(Html::encode($model->text)),
            ],

            [
                'label' => 'Дата обращения',
                'format' => 'html',
                'value' => Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_create),
            ],

            [
                'label' => 'Дата ответа',
                'format' => 'html',
                'value' => Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_close),
            ],

            [
                'label' => $model->getAttributeLabel('priority'),
                'format' => 'html',
                'value' => Yii::$app->BasisFormat->helper('Status')->getItem($model->priorityArray, $model->priority),
            ],

            [
                'label' => $model->getAttributeLabel('answered'),
                'format' => 'html',
                'value' => Yii::$app->BasisFormat->helper('Status')->getItem($model->answeredArray, $model->answered),
            ],

            /*[
                'label' => $model->getAttributeLabel('close'),
                'format' => 'html',
                'value' => Yii::$app->BasisFormat->helper('Status')->getItem($model->closeArray, $model->close),
            ],*/

            [
                'label' => $model->getAttributeLabel('department'),
                'format' => 'html',
                'value' => Yii::$app->BasisFormat->helper('Status')->getItem($model->departmentArray, $model->department),
            ],

            'ip',
            'user_agent',
        ],
    ]);

?>

<p><br/></p>





<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success indent-bottom">
        <span class="icon-checkmark-circle"></span> <?php echo Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('danger')): ?>
    <div class="alert alert-danger indent-bottom">
        <span class="icon-cancel-circle"></span> <?php echo Yii::$app->session->getFlash('danger') ?>
    </div>
<?php endif; ?>

<p><br/></p>


<div class="chat">
    <?php foreach($model->helpdeskAnswers as $answer): ?>
        <div class="message">
            <?php
                if ($model->user) {
                    echo \nepster\faceviewer\Widget::widget([
                        'template' => '<div class="user-face" style="float: left" title="{name} {surname}"><div class="avatar">{face}</div></div>',
                        'templateUrl' => ['/users/user/update', 'id' => $answer->user->id],
                        'templateUrlOptions' => ['class' => 'message-img'],
                        'data' => [
                            'name' => Html::encode($answer->user->profile->name),
                            'surname' => Html::encode($answer->user->profile->surname),
                            'avatar_url' => $answer->user->profile->avatar_url,
                        ],
                    ]);
                }
            ?>
            <div class="message-body">
                <?=nl2br(Html::encode($answer->text))?>
                <span class="attribution"><?=Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($answer->date)?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>



<?php $form = ActiveForm::begin(); ?>

<div class="block">
    <h6><i class="icon-bubbles4"></i> Ответить</h6>
    <?= $form->field($formModel, 'text')->textArea(['maxlength' => true, 'placeholder' => 'Текст сообщения...']) ?>
    <div class="message-controls">
        <div class="pull-right">
            <?= Html::submitButton(Yii::t('app', 'Ответ'), ['class' => 'btn btn-success btn-loading']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>