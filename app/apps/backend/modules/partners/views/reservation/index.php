<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\partners\models\ReservationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все брони';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление бронями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все брони',
            'url' => ['/partners/reservation/index'],
        ]
    ]
]);

/*echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/reservation/create'],
            'label' => 'Создать',
        ]
    ]
]);*/

echo $this->render('_search', ['model' => $searchModel]);

?>


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

<?php echo GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">{pager}</div></div>',
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => "{update} &nbsp; {delete}",
            'contentOptions' => ['class' => 'text-center tdControl'],
            'header' => 'Управление',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="icon-wrench"></span>', ['/partners/reservation/update', 'id' => $model->id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/partners/reservation/delete', 'id' => $model->id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'format' => 'html',
            'label' => 'Пользователь',
            'contentOptions' => ['class' => 'text-left tdUser'],
            'value' => function ($model) {
                return \nepster\faceviewer\Widget::widget([
                        'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname}</div>',
                        'templateUrl' => ['/users/user/update', 'id' => $model->user_id],
                        'data' => [
                            'email' => Html::encode($model->user->email),
                            'name' => Html::encode($model->user->profile->name),
                            'surname' => Html::encode($model->user->profile->surname),
                            'avatar_url' => $model->user->profile->avatar_url,
                        ]
                    ]) . '<div>EMAIL: <b>' . Html::encode($model->user->email) . '</b></div>';
            }
        ],

        [
            'attribute' => 'address',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {


                $address = 'Страна: ' . $model->city->country->name;
                $address .= ', Город: ' . $model->city->name;
                $address .= '<br/>';
                $address .= $model->address;

                return $address;
            },
        ],

        [
            'format' => 'html',
            'label' => 'Политика аренды',
            'contentOptions' => ['class' => 'text-left', 'style' => 'min-width: 200px'],
            'value' => function ($model) {
                $value = $model->rentTypeText . '<br/>' . $model->budgetString . '<br/>';
                $value .= 'Комнат: ' . $model->roomsText;
                $value .= '<br/> Спальных мест: ' . $model->bedsText;
                $value .= '<br/> Расстояние к метро: ' . $model->metroWalkText;
                $value .= '<br/> Этаж: ' . $model->floorText;

                $value .= '<br/> Кол-во человек: ' . $model->clientsCountText;
                $value .= '<br/> ' . $model->childrenText;
                $value .= '<br/> ' . $model->petsText;

                return $value;
            },
        ],

        [
            'label' => 'Статус заявки',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left', 'style' => 'width: 250px'],
            'value' => function ($model) {
                $value = 'Закрыта: ' . Yii::$app->BasisFormat->helper('Status')->booleanString($model->closed, true) . '<br/>';
                $value .= 'Отменена: ' . Yii::$app->BasisFormat->helper('Status')->booleanString($model->cancel, true) . '<br/>';
                $color = 'green';
                if ($model->date_actuality < date('Y-m-d H:i:s')) $color = 'red';
                $value .= 'Актуальна до: <span style = "color:' . $color . '">' . Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_actuality) . '</span>';
                return $value;
            }
        ],

        [
            'attribute' => 'date_create',
            'contentOptions' => ['class' => 'text-left tdDate'],
            'format' => 'basisDiffAgoPeriodRound',
        ],
    ],
]);

