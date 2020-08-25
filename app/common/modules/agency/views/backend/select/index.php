<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\agency\models\backend\search\SelectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Все заявки на "Быстро подберём квартиру"';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => $this->title,
    'description' => 'Все заявки',
    'breadcrumb' => [
        [
            'label' => 'Все заявками',
            'url' => ['/agency/select/index'],
        ]
    ]
]);

?>

<?php echo $this->render('_search', ['model' => $searchModel]); ?>


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

if (Yii::$app->user->can('agency-select-multi-control')) {
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
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">  ' . $actions . '  {pager}</div></div>',
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
                    return Html::a('<span class="icon-wrench"></span>', ['/agency/select/update', 'id' => $model->apartment_select_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        '<span class="icon-remove3"></span>',
                        ['/agency/select/delete', 'id' => $model->apartment_select_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]
                    );
                }
            ],
        ],

        [
            'attribute' => 'apartment_select_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdId'],
            'value' => function ($model) {
                return $model->apartment_select_id;
            }
        ],

        [
            'attribute' => 'name',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdUserFull'],
            'value' => function ($model) {
                return '<p>' . $model->name . '</p>' . $model->email . '<br />' . $model->phone . '<br />' . $model->phone2;
            }
        ],

        [
            'attribute' => 'description',
            'format' => 'html',
            'enableSorting' => false,
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                return 'Кол-во комнат: ' . $model->rooms . '<br/>' . $model->description;
            },
        ],

        [
            'attribute' => 'rent_types_array',
            'format' => 'html',
            'enableSorting' => false,
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                if (!$model->rent_types_array) {
                    return '(не задано)';
                }

                return implode('<br/>', array_intersect_key($model->rentTypesList, array_flip($model->rent_types_array)));
            },
        ],

        [
            'attribute' => 'metro_array',
            'format' => 'html',
            'enableSorting' => false,
            'value' => function ($model) {
                if (!$model->metro_array) {
                    return '(не задано)';
                }

                return implode('<br/>', array_intersect_key($model->metroStations, array_flip($model->metro_array)));
            },
        ],

        [
            'attribute' => 'status',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdStatus'],
            'value' => function ($model) {
                return $model->statusText;
            }
        ],

        [
            'attribute' => 'date_create',
            'format' => 'basisDiffAgoPeriodRound',
        ],
    ],
]);


echo Html::endForm();
