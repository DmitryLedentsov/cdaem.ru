<?php
/**
 * Все сео-тексты
 * @var yii\base\View $this Представление
 * @var $searchModel common\modules\seo\models\backend\SeotextSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Все сео-тексты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление сео-текстами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все сео-тексты',
            'url' => ['/seo/text/index'],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/seo/text/create'],
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

    <h3>Размещение сео-текстов на страницах сайта</h3> <br/>

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
                    return Html::a('<span class="icon-wrench"></span>', ['/seo/text/update', 'id' => $model->text_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/seo/text/delete', 'id' => $model->text_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'attribute' => 'url',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText', 'style' => 'vertical-align: top'],
            'value' => function ($model) {
                return Html::a(Yii::$app->params['siteDomain'] . $model->url, Yii::$app->params['siteDomain'] . $model->url);
            }
        ],

        [
            'attribute' => 'type',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdStatus', 'style' => 'vertical-align: top'],
            'value' => function ($model) {

                return \yii\helpers\ArrayHelper::getValue($model->typeArray, $model->type);
            }
        ],

        [
            'attribute' => 'text',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                return $model->text;
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
    ],
]);
