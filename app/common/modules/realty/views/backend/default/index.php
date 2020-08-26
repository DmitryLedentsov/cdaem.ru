<?php
/**
 * Все типы аренды
 * @var yii\base\View $this Представление
 * @var $searchModel common\modules\realty\models\backend\search\RentTypeSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Все типы аренды';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление типами аренды',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все типы аренды',
            'url' => ['/realty/default/index'],
        ]
    ]
]);

echo \common\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/realty/default/create'],
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


<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">  {pager}</div></div>',
    'dataProvider' => $dataProvider,
    'columns' => [

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => "{update}",
            'contentOptions' => ['class' => 'text-center tdControl'],
            'header' => 'Управление',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="icon-wrench"></span>', ['/realty/default/update', 'id' => $model->rent_type_id]);
                },
            ],
        ],

        [
            'attribute' => 'name',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                return Html::a($model->name, Yii::$app->params['siteDomain'] . '/' . $model->slug);
            }
        ],

        [
            'attribute' => 'slug',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                return $model->slug;
            },
        ],
        /*
        [
            'attribute' => 'visible',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdId'],
            'value' => function ($model) {
                return Yii::$app->formatter->asBoolean($model->visible);
            },
        ],
        */
    ],
]);
