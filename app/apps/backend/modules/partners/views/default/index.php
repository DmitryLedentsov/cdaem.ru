<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\partners\models\ApartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/partners/default/index'],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/partners/default/create'],
            'label' => 'Создать',
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

<br />

<?php

$actions = '';

if (Yii::$app->user->can('partners-apartment-multi-control')) {
    $actions = '';
    if (Yii::$app->user->can('partners-apartment-multi-control')) {
        $actions = '
            <div class="table-actions">
                <label>Действия:</label>
                <select name="action" class="form-control" style="display: inline-block; width: auto">
                    <option value=""></option>
                    <option value="moderated">Проверено</option>
                    <option value="unmoderated">На модерации</option>
                    <option value="delete">Удалить</option>
                </select>
                <button class="btn btn-primary" type="submit">OK</button>
            </div>';
    }
}



echo Html::beginForm(['multi-control']);

echo GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer"> ' . $actions . ' {pager}</div></div>',
    'dataProvider' => $dataProvider,
    'columns' => [
    
        [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['class' => 'text-left tdCheckbox'],
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => "{update} &nbsp; {delete}",
            'contentOptions' => ['class' => 'text-center tdControl'],
            'header' => 'Управление',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="icon-wrench"></span>', ['/partners/default/update', 'id' => $model->apartment_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/partners/default/delete', 'id' => $model->apartment_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'label' => 'Главное изображение',
            'value' => function ($model) {
                if (!empty($model->titleImage)) {
                    return ('
                        <div class="thumbnail thumbnail-boxed">
                            <div class="thumb">
                                '.Html::img($model->titleImage->previewSrc, ['alt' => $model->titleImage->preview]).'
                            </div>
                        </div>
                    ');
                }
                return null;
            },
        ],

        [
            'attribute' => 'user',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdUserFull'],
            'label' => 'Пользователь',
            'value' => function ($model) {
                return \nepster\faceviewer\Widget::widget([
                    'template' => '<div class="user-face">'. Html::a('Все апартаменты', '') .' <div class="avatar">{face}</div>{name} {surname} <p>ID {id}</p></div>',
                    'templateUrl' => ['/users/user/update', 'id' => $model->user->id],
                    'data' => [
                        'id' => $model->user->id,
                        'name' => Html::encode($model->user->profile->name),
                        'surname' => $model->user->profile->surname,
                        'avatar_url' => $model->user->profile->avatar_url,
                    ]
                ]).  '<div class="clearfix"></div> <br/> <div class="text-right">'. Html::a('Все апартаменты', ['/partners/default/index', 's[user_id]' => $model->user->id]) .'</div>';
            }
        ],

        [
            'attribute' => 'address',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {

                $id = '<b>№ ' . $model->apartment_id . '</b>';

                $address = '';
                if ($model->city) {
                    $address.= 'Страна: ' . $model->city->country->name;
                    $address.= ', Город: ' . $model->city->name;
                    $address.= '<br/>';
                }

                $address.= $model->address;
                $address.= $model->apartment ? ' к. ' . $model->apartment : '';

                return $id . "&nbsp; <p><br/></p> " . $address  . "<p><br/></p> " .  nl2br(Html::decode($model->description));
            },
        ],

        [
            'label' => 'Объявления',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                $adverts = [];
                foreach($model->adverts as $advert) {

                    $url = '';

                    if ($model->city) {
                        $url = str_replace('<city>', $model->city->name_eng, Yii::$app->params['siteSubDomain']) . '/flats/' . $advert->advert_id;
                    }

                    $adverts[] = Html::a('ID '.$advert->advert_id . ' ' . $advert->rentType->name . ' - ' . $advert->priceText, $url);
                }

                return implode($adverts, '<br/>');
            },
        ],

        [
            'attribute' => 'visible',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->booleanString($model->visible);
            },
        ],

        [
            'attribute' => 'suspicious',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->booleanString($model->suspicious, true);
            },
        ],

        [
            'attribute' => 'status',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->getItem($model->getStatusArray(), $model->status);
            },
        ],

        [
            'attribute' => 'date_create',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
        ],
    ],
]);

echo Html::endForm();
