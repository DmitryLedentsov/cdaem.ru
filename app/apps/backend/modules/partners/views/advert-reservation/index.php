<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\partners\models\AdvertReservationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все брони к объявлениям';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление бронями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все брони к объявлениям',
            'url' => ['/partners/advert-reservation/index'],
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

<br />

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
                    return Html::a('<span class="icon-wrench"></span>', ['/partners/advert-reservation/update', 'id' => $model->id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/partners/advert-reservation/delete', 'id' => $model->id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'attribute' => 'address',
            'label' => 'Адрес',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                $url = str_replace('<city>', $model->advert->apartment->city->name_eng, Yii::$app->params['siteSubDomain']) . '/flats/' . $model->advert_id;



                /*$advertHref = Html::a('ID ' . $model->advert_id, $url);
                $address = '<span style="color:blue">Заявка к обьявлению ' . $advertHref . '</span><br/>';
                $address.= 'Страна: ' . $model->advert->apartment->city->country->name;
                $address.= ', Город: ' . $model->advert->apartment->city->name;
                $address.= '<br/>';
                $address.= $model->advert->apartment->address;*/



                $titleImage = '';
                if (!empty($model->advert->apartment->titleImage)) {
                    $titleImage = ('
                        <div class="thumbnail thumbnail-boxed" style="float: none">
                            <div class="thumb">
                                '.Html::img($model->advert->apartment->titleImage->previewSrc, ['alt' => $model->advert->apartment->titleImage->preview]).'
                            </div>
                        </div>
                    ');
                }

                $id = '<b>' .  Html::a('ID ' . $model->advert_id, ['/partners/default/update', 'id' => $model->advert->apartment->apartment_id ]) . '</b>';

                $address = '';
                if ($model->advert->apartment->city) {
                    $address.= 'Страна: ' . $model->advert->apartment->city->country->name . ', ';
                    $address.= 'Город: ' . $model->advert->apartment->city->name;
                    $address.= '<br/>';
                }

                $address.= $model->advert->apartment->address;
                $address.= $model->advert->apartment->apartment ? ' к. ' . $model->advert->apartment->apartment : '';

                return  Html::a($titleImage, $url) . $id . "<br>" . $address;
            },
        ],

        [
            'format' => 'html',
            'label' => 'Клиент',
            'contentOptions' => ['class' => 'text-left tdUser'],
            'value' => function($model) {
                return \nepster\faceviewer\Widget::widget([
                    'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname}</div>',
                    'templateUrl' => ['/users/user/update', 'id' => $model->user_id],
                    'data' => [
                        'email' => Html::encode($model->user->email),
                        'name' => Html::encode($model->user->profile->name),
                        'surname' => Html::encode($model->user->profile->surname),
                        'avatar_url' => $model->user->profile->avatar_url,
                    ]
                ]) . '<div>EMAIL: <b>'.Html::encode($model->user->email).'</b></div>';
            }
        ],

        [
            'format' => 'html',
            'label' => 'Владелец',
            'contentOptions' => ['class' => 'text-left tdUser'],
            'value' => function($model) {
                return \nepster\faceviewer\Widget::widget([
                    'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname}</div>',
                    'templateUrl' => ['/users/user/update', 'id' => $model->landlord_id],
                    'data' => [
                        'email' => Html::encode($model->landlord->email),
                        'name' => Html::encode($model->landlord->profile->name),
                        'surname' => Html::encode($model->landlord->profile->surname),
                        'avatar_url' => $model->landlord->profile->avatar_url,
                    ]
                ]) . '<div>EMAIL: <b>'.Html::encode($model->landlord->email).'</b></div>';
            }
        ],

        [
            'format' => 'html',
            'label' => 'Политика аренды',
            'contentOptions' => ['class' => 'text-left', 'style' => 'min-width: 250px; width: 250px'],
            'value' => function($model) {
                $value = $model->advert->rentType->name . ' - ' . $model->advert->priceText;
                $value .= '<br>Кол-во человек: '.$model->clientsCountText;
                $value .= '<br>'.$model->childrenText;
                $value .= '<br>'.$model->petsText;

                return $value;
            },
        ],

        [
            'label' => 'Статус заявки',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left', 'style' => 'min-width: 250px; width: 250px'],
            'value' => function ($model) {
                $value = 'Закрыта:' . Yii::$app->BasisFormat->helper('Status')->booleanString($model->closed, true) . '<br/>';
                $value .= 'Отменена: ' . Yii::$app->BasisFormat->helper('Status')->booleanString($model->cancel, true) . '<br/>';
                $value .= 'Дата создания: ' . $model->date_create . '<br/>';
                $color = 'green';
                if ($model->date_actuality < date('Y-m-d H:i:s')) $color = 'red';
                $value .= 'Актуальна до: <span style = "color:' . $color . '">' . Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($model->date_actuality) . '</span>';
                return $value;
            }
        ],

        [
            'label' => 'Подтверждение',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdStatus'],
            'value' => function ($model) {
                $color = 'gray';
                if ($model->confirm) $color = 'blue';
                $value = '<span style = "color:' . $color . '">' . $model->confirmText . '</span><br/>';
                if ($model->failure) {
                    $color = 'red';
                    if ($model->failure->processed == 1 OR $model->failure->closed == 0) $color = 'green';
                    $value .= '<span style = "color:' . $color . '">"Незаезд"' . Html::a(' История ', ['/partners/reservation-failures/index', 's[reservation_id]'=> $model->id]). '</span><br/>';
                }

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

