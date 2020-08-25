<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\agency\models\backend\search\AdvertisementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Рекламные объявления';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление рекламными объявлениями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все рекламные объявления',
            'url' => ['/agency/advertisement/index'],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/advertisement/create'],
            'label' => 'Создать',
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

if (Yii::$app->user->can('agency-advertisement-multi-control')) {
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

<?= GridView::widget([
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
                    return Html::a('<span class="icon-wrench"></span>', ['/agency/advertisement/update', 'id' => $model->advertisement_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        '<span class="icon-remove3"></span>',
                        ['/agency/advertisement/delete', 'id' => $model->advertisement_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]
                    );
                }
            ],
        ],

        [
            'attribute' => 'advert_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdImage'],
            'value' => function ($model) {
                if (!empty($model->advert->apartment->titleImage)) {
                    return (
                        Html::tag('strong', $model->advert->rentType->name) .
                        '<br/><div class="thumbnail thumbnail-boxed">
                            <div class="thumb">
                                ' . Html::a(Html::img($model->advert->apartment->titleImage->previewSrc, ['alt' => $model->advert->apartment->titleImage->preview]), ['/agency/default/update', 'id' => $model->advert->apartment_id]) . '
                            </div>
                        </div>
                    ');
                }

                return null;
            },
        ],

        [
            'attribute' => 'text',
            'format' => 'text',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                return $model->text;
            },
        ],

        [
            'attribute' => 'date_start',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdDate'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_start);
            },
        ],

        [
            'attribute' => 'date_expire',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdDate'],
            'value' => function ($model) {
                $options = ['style' => 'color:green'];
                if (time() >= strtotime($model->date_expire)) {
                    $options['style'] = 'color:red';
                }

                return Html::tag('span', Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_expire), $options);
            },
        ]
    ],
]);


echo Html::endForm();
