<?php

use \yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\agency\models\backend\search\DetailsHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Все заявки на отправку реквизитов';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' =>  $this->title,
    'description' => 'Все заявки',
    'breadcrumb' => [
        [
            'label' => 'Все заявки',
            'url' => ['/agency/details/index'],
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

<br />

<?php

$actions = '';

if (Yii::$app->user->can('agency-apartment-multi-control')) {
    $actions = '
    <div class="table-actions">
        <label>Действия:</label>
        <select name="action" class="form-control" style="display: inline-block; width: auto">
            <option value=""></option>
            <option value="send-details">Отправить реквизиты</option>
        </select>
        <button class="btn btn-primary" type="submit">OK</button>
    </div>';
}

echo Html::beginForm(['multi-control'], 'post');

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
                    return Html::a('<span class="icon-wrench"></span>', ['/agency/details/update', 'id' => $model->id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/agency/details/delete', 'id' => $model->id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'attribute' => 'advert_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdImage'],
            'label' => 'Объявление',
            'value' => function ($model) {
                if (!empty($model->advert->apartment->titleImage)) {
                    return (
                        Html::tag('strong', $model->advert->rentType->name) .
                        '<div class="thumbnail thumbnail-boxed">
                            <div class="thumb">
                                '.Html::a(Html::img($model->advert->apartment->titleImage->previewSrc, ['alt' => $model->advert->apartment->titleImage->preview]), ['/agency/default/update', 'id' => $model->advert->apartment_id]).'
                            </div>
                        </div>
                    ');
                }
                return null;
            },
        ],

        [
            'attribute' => 'contacts',
            'format' => 'html',
            'label' => 'Контакты',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return '<p>' .  $model->phone . '</p>' . '<p>' .  Html::encode($model->email) . '</p>';
            }
        ],

        [
            'attribute' => 'type',
            'format' => 'html',
            'label' => 'Подробнее',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return '<p>' .  ArrayHelper::getValue($model->getTypeArray(), $model->type) . '</p>' . '<p>' .  ArrayHelper::getValue($model->getPaymentArray(), $model->payment) . '</p>';
            }
        ],

        [
            'attribute' => 'date_create',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
            'value' => function ($model) {
                return $model->date_create;
            }
        ],

        [
            'attribute' => 'processed',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->booleanString($model->processed);
            }
        ],

    ],
]);

echo Html::endForm();

