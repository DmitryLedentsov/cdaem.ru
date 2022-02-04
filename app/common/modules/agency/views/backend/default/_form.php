<?php

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Apartment */
/* @var $formModel common\modules\agency\models\backend\form\ApartmentForm */

/* @var $advertsDataProvider yii\data\activeDataProvider */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

\common\modules\agency\assets\backend\AgencyAsset::register($this);
?>


<?php if (!$model->isNewRecord) : ?>

    <h6 class="heading-hr"><span class="icon-share"></span> Объявления</h6>

    <?php if (Yii::$app->user->can('agency-advert-create')) : ?>
        <div class="form-group">
            <?= Html::a('Добавить объявление', ['/agency/adverts/create', 'id' => $model->apartment_id], ['class' => 'btn btn-primary']) ?>
        </div>
    <?php endif; ?>

    <?php
    if (Yii::$app->user->can('agency-advert-view')) {
        echo GridView::widget([
            'tableOptions' => ['class' => 'table table-bordered'],
            'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div></div>',
            'dataProvider' => $advertsDataProvider,
            'columns' => [

                [
                    'attribute' => 'rent_type',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Html::a('ID' . $model->advert_id . ' - ' . $model->rentType->name, Yii::$app->params['siteDomain'] . '/advert/' . $model->advert_id);
                    },
                ],

                [
                    'attribute' => 'price',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model->priceText;
                    },
                ],

                [
                    'attribute' => 'main_page',
                    'format' => 'html',
                    'contentOptions' => ['class' => 'text-left tdStatus'],
                    'value' => function ($model) {
                        return Yii::$app->BasisFormat->helper('Status')->getItem($model->mainPageArray, $model->main_page);
                    },
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => "{update} &nbsp; {delete}",
                    'contentOptions' => ['class' => 'text-center tdControl'],
                    'header' => 'Управление',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="icon-wrench"></span>', ['/agency/adverts/update', 'id' => $model->advert_id]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<span class="icon-remove3"></span>',
                                ['/agency/adverts/delete', 'id' => $model->advert_id],
                                ['data' => [
                                    'confirm' => 'Удалить?',
                                ]]
                            );
                        }
                    ],
                ],
            ],
        ]);
    }
    ?>

<?php endif; ?>


    <p><br/></p>


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

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>


<?php if ($formModel->scenario == 'update') : ?>

    <h6 class="heading-hr"><span class="icon-images"></span> Изображения</h6>

    <div class="row">
        <div id="controlImages" data-sort-url="<?php echo Url::toRoute(['/agency/default/sort-images']) ?>">
            <?php foreach ($model->orderedImages as $image) : ?>
                <div id="<?= $image->image_id ?>"
                     class="thumbnail <?= $image->default_img ? 'active' : '' ?> thumbnail-boxed"
                     data-sort="<?= $image->sort ?>">
                    <div class="thumb">
                        <img alt="<?= $image->alt ?>" src="<?= $image->previewSrc ?>">
                        <div class="thumb-options">
                            <span>
                                <?php
                                if ($image->default_img == 0) {
                                    echo Html::a('<i class="icon-lamp2"></i>', ['/agency/default/default-image', 'id' => $image->image_id], ['class' => 'btn btn-icon btn-success']);
                                }
                                ?>
                                <?= Html::a('<i class="icon-remove"></i>', ['/agency/default/delete-image', 'id' => $image->image_id], ['class' => 'btn btn-icon btn-success']) ?>
                                <?= Html::a('<i class="icon-text-width"></i>', ['/agency/default/update-image', 'id' => $image->image_id], ['class' => 'btn btn-icon btn-success update-image-btn']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    Modal::begin(['header' => 'Метатеги', 'id' => 'imageModal']);
    echo '<div id="modal-image-content"></div>';
    Modal::end();
    ?>

    <br/>

<?php endif; ?>


    <h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>
    <div class="row">
        <?php if ($formModel->scenario == 'update') : ?>
            <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
                <?php
                $user = '';
                if (!$formModel->scenario == 'create') {
                    if ($model->user) {
                        $user = Html::a($model->user->profile->name . ' ' . $model->user->profile->surname, ['/users/user/update', 'id' => $model->user->id]);
                    }
                }
                ?>
                <?= $form->field($formModel, 'user_id', [
                    'template' => '{label}{input}' . $user . '{error}',
                ])->textInput(['maxlength' => true]) ?>
            </div>
        <?php endif; ?>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-6 col-lg-2"><?= $form->field($formModel, 'visible')->dropDownList(Yii::$app->formatter->booleanFormat) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'files[]')->fileInput(['class' => 'styled', 'multiple' => 'true']) ?></div>
    </div>
<?php if (!$model->isNewRecord) : ?>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'date_create')->textInput(['maxlength' => true, 'disabled' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($formModel, 'date_update')->textInput(['maxlength' => true, 'disabled' => true]) ?></div>
    </div>
<?php endif; ?>

    <h6 class="heading-hr"><i class="icon-home6"></i> Апартаменты</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'apartment') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'floor') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'total_rooms')->dropDownList($model->roomsList) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'total_area') ?></div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'beds') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'remont')->dropDownList($model->remontList) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'metro_walk')->dropDownList($model->metroWalkList) ?></div>
    </div>

    <h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>
    <div class="row">
        <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
            <?= $form->field($formModel, 'description')->widget(\vova07\imperavi\Widget::class, [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 200,
                    'imageUpload' => Url::to(['/agency/default/image-upload']),
                    'formattingAdd' => new \yii\web\JsExpression('[{title: "Очистить форматирование",  func: "inline.removeFormat"}]'),
                    'buttons' => [
                        'html', 'formatting', 'bold', 'italic', 'deleted', 'underline',
                        'unorderedlist', 'orderedlist', 'outdent', 'indent',
                        'image', 'link', 'alignment', 'horizontalrule'
                    ],
                    'plugins' => [
                        'fontcolor',
                        'fontsize',
                        'fontfamily',
                        'table',
                        'fullscreen',
                        'imagemanager'
                    ]
                ]
            ]);
            ?>
        </div>
    </div>


    <h6 class="heading-hr"><i class="icon-arrow-up5"></i> Адрес</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
            <?php
            $city1 = '';
            if (!$formModel->scenario == 'create') {
                if ($model->city) {
                    $city1 = $model->city->name;
                }
            }
            ?>
            <?= $form->field($formModel, 'city_id', [
                'template' => '{label}{input}' . $city1 . '{error}',
            ])->textInput([
                'class' => 'form-control select-city'
            ]) ?>
        </div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
            <?= $form->field($formModel, 'closest_city_id')->textInput([
                'template' => '{label}{input}{error}',
                'class' => 'form-control select-city'
            ]) ?>
        </div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'district1')->dropDownList($model->districtsList, ['prompt' => '']) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'district2')->dropDownList($model->districtsList, ['prompt' => '']) ?></div>
    </div>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($formModel, 'address') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-9">
            <?= $form->field($formModel, 'metroStationsArray', [
                'template' => '<br/><p>{label}</p><p>{error}</p><div style="max-height: 150px; overflow: auto; overflow-y: scroll">{input}</div>',
            ])->checkBoxList($model->metroList) ?>
        </div>
    </div>

    <br/>

<?php if (Yii::$app->user->can('agency-apartment-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

    <br/>

<?php ActiveForm::end(); ?>
