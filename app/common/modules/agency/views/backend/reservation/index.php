<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\agency\models\backend\search\ReservationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все брони';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление бронями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все брони',
            'url' => ['/agency/reservation/index'],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/reservation/create'],
            'label' => 'Создать',
        ]
    ]
]);

echo $this->render('_search', ['model' => $searchModel]);

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
$actions = '';

if (Yii::$app->user->can('agency-reservation-multi-control')) {
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


echo Html::beginForm(['multi-control'], 'post'); ?>

<?php echo GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">  ' . $actions . ' {pager}</div></div>',
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
                    return Html::a('<span class="icon-wrench"></span>', ['/agency/reservation/update', 'id' => $model->reservation_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        '<span class="icon-remove3"></span>',
                        ['/agency/reservation/delete', 'id' => $model->reservation_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]
                    );
                }
            ],
        ],

        [
            'attribute' => 'reservation_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdId'],
            'value' => function ($model) {
                return $model->reservation_id;
            },
        ],

        [
            'label' => 'Заглавная картинка',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdImage'],
            'value' => function ($model) {
                if (!empty($model->apartment->titleImage)) {
                    return ('
                        <div class="thumbnail thumbnail-boxed">
                            <div class="thumb">
                                ' . Html::a(Html::img($model->apartment->titleImage->previewSrc, ['alt' => $model->apartment->titleImage->preview]), ['/agency/default/update', 'id' => $model->apartment_id]) . '
                            </div>
                        </div>
                    ');
                }

                return null;
            },
        ],

        [
            'attribute' => 'name',
            'format' => 'text',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->name;
            },
        ],

        [
            'attribute' => 'contact',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return 'EMAIL: ' . $model->email . '<br/>' . ' Телефон: ' . $model->phone;
            },
        ],

        [
            'attribute' => 'clients_count',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->clients_count;
            },
        ],

        [
            'attribute' => 'more_info',
            'format' => 'text',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->more_info;
            },
        ],

        [
            'attribute' => 'processed',
            'format' => 'BasisBooleanString',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->processed;
            },
        ],

        [
            'attribute' => 'date_reserve',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdDate'],
            'value' => function ($model) {
                return
                    'Дата заезда: <br/>' . Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_arrived) . '<br/><br/>' .
                    'Дата выезда: <br/>' . Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_out);
            },
        ],


        [
            'attribute' => 'date_create',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
            'value' => function ($model) {
                return $model->date_create;
            },
        ],
    ],
]);

echo Html::endForm();
