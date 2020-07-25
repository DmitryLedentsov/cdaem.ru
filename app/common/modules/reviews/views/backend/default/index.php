<?php
/**
 * Все отзывы
 * @var yii\base\View $this Представление
 * @var $model common\modules\reviews\models\Review
 * @var $searchModel common\modules\reviews\models\ReviewSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Все отзывы';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление отзывами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все отзывы',
            'url' => ['/reviews/default/index'],
        ]
    ]
]);

echo \backend\modules\admin\widgets\ExtraControlWidget::widget([
    'control' => [
        [
            'url' => ['/reviews/default/create'],
            'label' => 'Создать',
        ]
    ]
]);
?>

<?php echo $this->render('_search', [
    'model' => $model,
    'searchModel' => $searchModel,
]); ?>


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

if (Yii::$app->user->can('reviews-multi-control')) {
    $actions = '';
    if (Yii::$app->user->can('reviews-multi-control')) {
        $actions = '
            <div class="table-actions">
                <label>Действия:</label>
                <select name="action" class="form-control" style="display: inline-block; width: auto">
                    <option value=""></option>
                    <option value="solve">Разрешить</option>
                    <option value="forbid">Запретить</option>
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
                    return Html::a('<span class="icon-wrench"></span>', ['/reviews/default/update', 'id' => $model->review_id]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="icon-remove3"></span>', ['/reviews/default/delete', 'id' => $model->review_id],
                        ['data' => [
                            'confirm' => 'Удалить?',
                        ]]);
                }
            ],
        ],

        [
            'attribute' => 'apartment_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdId'],
            'value' => function ($model) {
                if (!empty($model->apartment->titleImage)) {
                    return ('
                        <div class="thumbnail thumbnail-boxed">
                            <div class="thumb">
                                ' . Html::a(Html::img($model->apartment->titleImage->previewSrc, ['alt' => $model->apartment->titleImage->preview]), ['/partners/default/update', 'id' => $model->apartment->apartment_id]) . '
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
            'value' => function ($model) {
                return \nepster\faceviewer\Widget::widget([
                        'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname}</div>',
                        'templateUrl' => ['/users/user/update', 'id' => $model->user->id],
                        'data' => [
                            'name' => Html::encode($model->user->profile->name),
                            'surname' => Html::encode($model->user->profile->surname),
                            'avatar_url' => $model->user->profile->avatar_url,
                        ]
                    ]) . '<div class="clearfix"></div> <br/> <div class="text-right">' . Html::a('Все отзывы', ['/reviews/default/index', 's[user_id]' => $model->user->id]) . '</div>';;
            }
        ],

        [
            'attribute' => 'text',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdText'],
            'value' => function ($model) {
                return nl2br(Html::encode($model->text));
            }
        ],

        [
            'attribute' => 'rating',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left tdStatus'],
            'value' => function ($model) {
                $rating = '';
                $rating .= '<p>Описание:<br> ' . Yii::$app->BasisFormat->helper('Status')->getItem($model->ratingMatchDescriptionArray, $model->match_description) . '</p>';
                $rating .= '<p>Цена и Качество:<br> ' . Yii::$app->BasisFormat->helper('Status')->getItem($model->ratingPriceAndQualityArray, $model->price_quality) . '</p>';
                $rating .= '<p>Чистота:<br> ' . Yii::$app->BasisFormat->helper('Status')->getItem($model->ratingCleanlinessArray, $model->cleanliness) . '</p>';
                $rating .= '<p>Заселение:<br> ' . Yii::$app->BasisFormat->helper('Status')->getItem($model->ratingEntryArray, $model->entry) . '</p>';
                return $rating;
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
            'attribute' => 'moderation',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdStatus'],
            'value' => function ($model) {
                return Yii::$app->BasisFormat->helper('Status')->booleanString($model->moderation);
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

echo Html::endForm();
