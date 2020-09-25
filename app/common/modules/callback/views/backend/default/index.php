<?php

/**
 * Все заявки на обратный звонок
 * @var yii\base\View $this Представление
 * @var $searchModel backend\modules\callback\models\CallbackSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Все заявки на обратный звонок';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление обратными звонками',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявки на обратный звонок',
            'url' => ['/callback/default/index'],
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

if (Yii::$app->user->can('callback-multi-control')) {
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
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">   ' . $actions . '  {pager}</div></div>',
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
                    return Html::a('<span class="icon-wrench"></span>', ['/callback/default/update', 'id' => $model->callback_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        '<span class="icon-remove3"></span>',
                        ['/callback/default/delete', 'id' => $model->callback_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]
                    );
                }
            ],
        ],

        [
            'attribute' => 'phone',
            'format' => 'text',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->phone;
            }
        ],

        [
            'attribute' => 'active',
            'format' => 'BasisBooleanString',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return $model->active;
            }
        ],

        [
            'attribute' => 'date_create',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return $model->date_create;
            }
        ],

        [
            'attribute' => 'date_processed',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return $model->date_processed;
            }
        ],

    ],
]);

echo Html::endForm();
