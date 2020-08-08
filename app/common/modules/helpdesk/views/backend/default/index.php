<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\modules\helpdesk\models\Helpdesk */
/* @var $searchModel common\modules\helpdesk\models\backend\HelpdeskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все тикеты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Техническая поддержка',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все тикеты',
            'url' => ['/helpdesk/default/index'],
        ]
    ]
]);

?>


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

    <br/>


<?php

echo $this->render('_search', [
    'searchModel' => $searchModel,
    'model' => $model
]);

$actions = '';

if (Yii::$app->user->can('helpdesk-multi-control')) {
    $actions = '
    <div class="table-actions">
        <label>Действия:</label>
        <select name="action" class="form-control" style="display: inline-block; width: auto">
            <option value=""></option>
            <option value="delete">Удалить</option>
        </select>
        <button class="btn btn-primary" type="submit">OK</button>
    </div>';
}

echo Html::beginForm(['multi-control']);

echo GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer"> ' . $actions . ' {pager}</div></div>',
    'dataProvider' => $dataProvider,
    'columns' => [

        [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['class' => 'text-left tdCheckbox'],
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => "{update} &nbsp; {delete}",
            'contentOptions' => ['class' => 'text-center tdControl'],
            'header' => 'Управление',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="icon-wrench"></span>', ['/helpdesk/default/update', 'id' => $model->ticket_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        '<span class="icon-remove3"></span>',
                        ['/helpdesk/default/delete', 'id' => $model->ticket_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]
                    );
                }
            ],
        ],

        [
            'attribute' => 'ticket_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdId'],
            'value' => function ($model) {
                return $model->ticket_id;
            },
        ],

        [
            'attribute' => 'user',
            'format' => 'html',
            'label' => 'Пользователь',
            'contentOptions' => ['class' => 'text-left tdUserFull'],
            'value' => function ($model) {
                if ($model->user_id === null) {
                    return Html::encode($model->user_name) . '<br/>' . Html::encode($model->email);
                } else {
                    return \nepster\faceviewer\Widget::widget([
                            'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname}</div>',
                            'templateUrl' => ['/users/user/update', 'id' => $model->user->id],
                            'data' => [
                                'email' => Html::encode($model->email),
                                'name' => Html::encode($model->user->profile->name),
                                'surname' => Html::encode($model->user->profile->surname),
                                'avatar_url' => $model->user->profile->avatar_url,
                            ]
                        ]) . '<div>EMAIL: <b>' . Html::encode($model->user->email) . '</b></div>';
                }
            }
        ],

        [
            'attribute' => 'department',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->getItem($model->departmentArray, $model->department);
            },
        ],

        [
            'attribute' => 'theme',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left '],
            'value' => function ($model) {
                return Html::a(Html::encode($model->theme), ['default/view', 'id' => $model->ticket_id]);
            },
        ],

        [
            'attribute' => 'priority',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->getItem($model->priorityArray, $model->priority);
            },
        ],

        [
            'attribute' => 'answered',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->getItem($model->answeredArray, $model->answered);
            },
        ],

        [
            'attribute' => 'date_create',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
        ],

        [
            'attribute' => 'source_type',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdStatus'],
            'value' => function ($model) {
                if ($model->source_type) {
                    return Yii::$app->BasisFormat->helper('Status')->getItem($model->sourceTypeArray, $model->source_type);
                }

                return null;
            },
        ],
    ],
]);

echo Html::endForm();
