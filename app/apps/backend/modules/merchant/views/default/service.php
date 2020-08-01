<?php

/**
 * История денежного оборота
 * @var yii\base\View $this Представление
 * @var $searchModel backend\modules\merchant\models\PaymentSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'История оплаты сервисов';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Мерчант',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Мерчант',
            'url' => ['/merchant/default/service'],
        ],

        [
            'label' => 'История оплаты сервисов',
        ],
    ]
]);
?>

<?php echo $this->render('service_search', ['model' => $searchModel]); ?>

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
                        ]) . '<div class="clearfix"></div> <br/> <div class="text-right">' . Html::a('Все операции', ['/merchant/default/service', 's[user_id]' => $model->user->id]) . '</div>';
                }
            }
        ],

        [
            'attribute' => 'service',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {

                $serviceName = Yii::$app->BasisFormat->helper('Status')->getItem(
                    Yii::$app->getModule('merchant')->systems['partners'],
                    $model->service
                );

                return $serviceName;
            }
        ],

        [
            'attribute' => 'payment_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                if (empty($model->payment_id)) {
                    return Html::tag('span', 'Не оплачен', ['style' => 'color: red']);
                }

                /*
                if ($model->payment) {

                    dd($model, $model->payment->funds);
                }
                dd($model->payment);*/

                return Html::a('№' . $model->payment_id, [
                    '/merchant/default/index',
                    's[payment_id]' => $model->payment_id
                ]);
            }
        ],

        [
            'attribute' => 'date_start',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
            'value' => function ($model) {
                return $model->date_start;
            }
        ],

        [
            'attribute' => 'date_expire',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
            'value' => function ($model) {
                return $model->date_expire;
            }
        ],

    ],
]);
