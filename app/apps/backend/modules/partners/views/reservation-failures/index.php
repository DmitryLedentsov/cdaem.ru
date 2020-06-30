<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\partners\models\ReservationFailuresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все заявки "Незаезд"';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление заявками "Незаезд"',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявки "Незаезд"',
            'url' => ['/partners/reservation-failures/index'],
        ]
    ]
]);

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
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => "{update} &nbsp; {delete}",
            'contentOptions' => ['class' => 'text-center tdControl'],
            'header' => 'Управление',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="icon-wrench"></span>', ['/partners/reservation-failures/update', 'id' => $model->id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/partners/reservation-failures/delete', 'id' => $model->id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'format' => 'html',
            'attribute' => 'user_id',
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
            'format' => 'html',
            'attribute' => 'reservation_id',
            'value' => function ($model) {
                return Html::a('Бронь № ' . $model->reservation_id, ['/partners/advert-reservation/update', 'id' => $model->reservation_id]);
            }
        ],

        [
            'format' => 'text',
            'attribute' => 'cause_text',
            'value' => function ($model) {
                if (mb_strlen($model->cause_text) > 205) {
                    $value = substr($model->cause_text, 0, 205) . '...';

                    return $value;
                }

                return $model->cause_text;
            }
        ],

        [
            'attribute' => 'processed',
            'format' => 'html',
            'value' => function ($model) {
                $color = 'red';
                if ($model->processed) $color = 'green';
                $value = Yii::$app->BasisFormat->helper('Status')->booleanString($model->processed);

                return $value;
            }
        ],

        [
            'attribute' => 'conflict',
            'format' => 'html',
            'value' => function ($model) {
                $value = Yii::$app->BasisFormat->helper('Status')->getItem($model->conflictArray, $model->conflict);

                return $value;
            }
        ],

        [
            'attribute' => 'closed',
            'format' => 'html',
            'value' => function ($model) {
                $value = Yii::$app->BasisFormat->helper('Status')->getItem($model->closedArray, $model->closed);

                return $value;
            }
        ],

        [
            'attribute' => 'date_to_process',
            'format' => 'basisFullDateTime',
        ],

        [
            'attribute' => 'date_create',
            'format' => 'basisDiffAgoPeriodRound',
        ],
    ],
]);

