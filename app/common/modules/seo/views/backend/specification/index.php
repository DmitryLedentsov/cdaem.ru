<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\seo\models\backend\SeoSpecificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все сео спецификации';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление сео спецификациями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => $this->title,
            'url' => ['/seo/specification/index'],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/seo/specification/create'],
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
            'template' => "{update} &nbsp; {delete}",
            'contentOptions' => ['class' => 'text-center tdControl'],
            'header' => 'Управление',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="icon-wrench"></span>', ['/seo/specification/update', 'id' => $model->id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/seo/specification/delete', 'id' => $model->id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'attribute' => 'url',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                $url = Yii::$app->params['siteDomain'];
                if ($model->city) {
                    $url = str_replace('<city>', $model->city, Yii::$app->params['siteSubDomain']);
                }
                $url .= $model->url;

                return Html::a($url, $url);
            }
        ],

        [
            'attribute' => 'title',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->title;
            }
        ],

        [
            'attribute' => 'description',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->description;
            }
        ],

        [
            'attribute' => 'keywords',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->keywords;
            }
        ],

        [
            'attribute' => 'date_create',
            'format' => 'basisFullDateTime'
        ],

        [
            'attribute' => 'date_update',
            'format' => 'basisFullDateTime'
        ]
    ],
]);
