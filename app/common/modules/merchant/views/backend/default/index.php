<?php

/**
 * История денежного оборота
 * @var yii\base\View $this Представление
 * @var $searchModel common\modules\merchant\models\backend\PaymentSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'История денежного оборота';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Мерчант',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Мерчант',
            'url' => ['/merchant/default/index'],
        ],
        [
            'label' => 'История денежного оборота',
        ],
    ]
]);
?>

<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">  {pager}</div></div>',
    'dataProvider' => $dataProvider,
    'columns' => [

        [
            'attribute' => 'user',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdUserFull'],
            'value' => function ($model) {
                if ($model->user) {
                    return \nepster\faceviewer\Widget::widget([
                            'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname}</div>',
                            'templateUrl' => ['/users/user/update', 'id' => $model->user->id],
                            'data' => [
                                'name' => Html::encode($model->user->profile->name),
                                'surname' => Html::encode($model->user->profile->surname),
                                'avatar_url' => $model->user->profile->avatar_url,
                            ]
                        ]) . '<div class="clearfix"></div> <br/> <div class="text-right">' . Html::a('Все операции', ['/merchant/default/index', 's[user_id]' => $model->user->id]) . '</div>';
                }
            }
        ],

        [
            'attribute' => 'date',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
            'value' => function ($model) {
                return $model->date;
            }
        ],

        [
            'attribute' => 'module',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->module;
            }
        ],

        [
            'attribute' => 'type',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->getItem($model->typeArray, $model->type);
            }
        ],

        [
            'attribute' => 'system',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                if ($model->system) {
                    return Yii::$app->BasisFormat->helper('Status')->getItem(Yii::$app->getModule('merchant')->systems[$model->module], $model->system);
                }
            }
        ],

        [
            'attribute' => 'funds',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model->funds, 'rub');
            }
        ],

    ],
]);
