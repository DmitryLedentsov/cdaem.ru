<?php

/**
 * История денежного оборота
 * @var yii\base\View $this Представление
 * @var $searchModel backend\modules\merchant\models\PaymentSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $paidAdverts array
 */

use common\modules\partners\models\Service;
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
            'value' => function (Service $model) use ($paidAdverts) {

                $serviceName = Yii::$app->BasisFormat->helper('Status')->getItem(
                    Yii::$app->getModule('merchant')->systems['partners'],
                    $model->service
                );

                $result = '';
                $advertIdList = $apartmentIdList = [];

                foreach ($model->getSelectedAdvertIdList() as $advertId) {
                    if (isset($paidAdverts[$advertId])) {
                        $advertIdList[$advertId] = $paidAdverts[$advertId]->advert_id;
                        $apartmentIdList[$paidAdverts[$advertId]->apartment_id] = $paidAdverts[$advertId]->apartment_id;
                    } else {
                        $result .= Html::tag('span', '№' . $advertId, ['style' => 'color: red']) . '  ';
                    }
                }

                $result.= Html::tag('div', 'Объявления', ['style' => 'margin-top: 7px;']);
                foreach ($advertIdList as $advertId) {
                    $result .= Html::a('№' . $advertId, ['/partners/adverts/update', 'id' => $advertId]) . '  ';
                }

                $result.= Html::tag('div', 'Апартаменты', ['style' => 'margin-top: 7px;']);
                foreach ($apartmentIdList as $apartmentId) {
                    $result .= Html::a('№' . $apartmentId, ['/partners/default/update', 'id' => $apartmentId]) . '  ';
                }

                return Html::tag('div', Html::tag('strong', $serviceName)) . $result;
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

                $paymentLink = Html::a('№' . $model->payment_id, [
                    '/merchant/default/index',
                    's[payment_id]' => $model->payment_id
                ]);

                $fundsInfo = Yii::$app->formatter->asCurrency($model->payment->funds, 'rub');;
                $fundsInfo = Html::tag('div', sprintf('Общая сумма: %s', $fundsInfo));

                return $paymentLink . $fundsInfo;
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
