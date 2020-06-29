<?php
/**
 * Все статьи
 * @var yii\base\View $this Представление
 * @var $model common\modules\articles\models\Article
 * @var $searchModel common\modules\articles\models\backend\ArticleSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Все статьи';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление статьями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все статьи',
            'url' => ['/articles/default/index'],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/articles/default/create'],
            'label' => 'Создать',
        ]
    ]
]);
?>

<?php echo $this->render('_search', ['searchModel' => $searchModel, 'model' => $model]); ?>



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
                    return Html::a('<span class="icon-wrench"></span>', ['/articles/default/update', 'id' => $model->article_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/articles/default/delete', 'id' => $model->article_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'attribute' => 'slug',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                if ($model->city) {
                    $url = str_replace('<city>', $model->city, Yii::$app->params['siteSubDomain']);
                    return Html::a($url . '/stati/' . $model->slug, $url . '/stati/' . $model->slug);
                }
                return Html::a(Yii::$app->params['siteDomain'] . '/stati/' . $model->slug, Yii::$app->params['siteDomain'] . '/stati/' . $model->slug);
            }
        ],
                
                
         

        [
            'attribute' => 'name',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return Html::a($model->name, Yii::$app->params['siteDomain'] . '/stati/' . $model->slug );
            }
        ],

        [
            'attribute' => 'status',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->getItem($model->statusArray, $model->status);
            }
        ],

        [
            'attribute' => 'visible',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->booleanString($model->visible);
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
    ],
]);
