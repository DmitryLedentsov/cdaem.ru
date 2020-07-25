<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\agency\models\backend\search\ApartmentSearch */
/* @var $model common\modules\agency\models\Apartment */
/* @var $formModel common\modules\agency\models\backend\form\ApartmentForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все апартаменты';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/agency/default/index'],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/agency/default/create'],
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

    <br/>

<?php

$actions = '';

if (Yii::$app->user->can('agency-apartment-multi-control')) {
    $actions = '
    <div class="table-actions">
        <label>Действия:</label>
        <select name="action" class="form-control" style="display: inline-block; width: auto">
            <option value=""></option>
            <option value="adverts">Объявления</option>
            <option value="specials">Спецпредложения</option>
            <option value="advertisement">Реклама</option>
            <option value="invisible">Не отображаются</option>
            <option value="visible">Отображаются</option>
            <option value="delete">Удалить</option>
        </select>
        <button class="btn btn-primary" type="submit">OK</button>
    </div>';
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
                    return Html::a('<span class="icon-wrench"></span>', ['/agency/default/update', 'id' => $model->apartment_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/agency/default/delete', 'id' => $model->apartment_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'label' => 'Заглавная картинка',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdImage'],
            'value' => function ($model) {
                if (!empty($model->titleImage)) {
                    return ('
                        <div class="thumbnail thumbnail-boxed">
                            <div class="thumb">
                                ' . Html::img($model->titleImage->previewSrc, ['alt' => $model->titleImage->preview]) . '
                            </div>
                        </div>
                    ');
                }
                return null;
            },
        ],

        [
            'attribute' => 'address',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {

                $id = '<b>№ ' . $model->apartment_id . '</b>';

                $address = 'Страна: ' . $model->city->country->name;
                $address .= ', Город: ' . $model->city->name;
                $address .= ', Район: ' . $model->mainDistrict->district_name;
                $address .= '<br/>';
                $address .= $model->address;
                $address .= $model->apartment ? 'к. ' . $model->apartment : '';

                return $id . "&nbsp; <p><br/></p> " . $address;
            },
        ],

        [
            'label' => 'Объявления',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                if (!$model->adverts) return '(не задано)';
                $adverts = [];
                foreach ($model->adverts as $advert) {
                    $adverts[] = Html::a('ID ' . $advert->advert_id . ' ' . $advert->rentType->name . ' - ' . $advert->priceText, Yii::$app->params['siteDomain'] . '/advert/' . $advert->advert_id);
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
            'attribute' => 'date_create',
            'format' => 'BasisFullDateTime',
            'contentOptions' => ['class' => 'text-center tdDate'],
        ],
    ],
]);

echo Html::endForm();
