<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\partners\assets\PartnersAsset;

/* @var $this yii\web\View */
/* @var $model backend\modules\partners\models\Apartment */
/* @var $form yii\widgets\ActiveForm */

PartnersAsset::register($this);
?>


<?php if (!$model->isNewRecord) : ?>


    <h6 class="heading-hr"><span class="icon-share"></span> Объявления</h6>

    <div class="form-group">
        <?= Html::a('Добавить объявление', ['/partners/adverts/create', 'id' => $model->apartment_id], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php
    
        echo GridView::widget([
            'tableOptions' => ['class' => 'table table-bordered'],
            'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div></div>',
            'dataProvider' => $advertsDataProvider,
            'columns' => [

                [
                    'format' => 'html',
                    'attribute' => 'rent_type',
                    'value' => function ($model) {
                        return Html::a($model->rentType->name, str_replace('<city>', $model->apartment->city->name_eng, Yii::$app->params['siteSubDomain']) . '/flats/' . $model->advert_id);
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => "{update} &nbsp; {delete}",
                    'contentOptions' => ['class' => 'text-center tdControl'],
                    'header' => 'Управление',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="icon-wrench"></span>', ['/partners/adverts/update', 'id' => $model->advert_id]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="icon-remove3"></span>', ['/partners/adverts/delete', 'id' => $model->advert_id],
                                ['data' => [
                                    'confirm' => 'Удалить?',
                                ]]);
                        }
                    ],
                ],
            ],
        ]);
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

<br />


<?php if (!$model->isNewRecord) : ?>

    <h6 class="heading-hr"><span class="icon-images"></span> Изображения</h6>

    <div class="row">
        <div id="controlImages" data-sort-url="<?php echo Url::toRoute(['/partners/default/sort-images']) ?>">
            <?php foreach ($model->orderedImages as $image) : ?>
                
                    <div id="<?= $image->image_id ?>" class="thumbnail <?= $image->default_img ? 'active' : '' ?> thumbnail-boxed" data-sort="<?=$image->sort?>">
                        <div class="thumb">
                            <img src="<?= $image->previewSrc ?>" alt="">
                            <div class="thumb-options">
                                <span>
                                    <?=Html::a('<i class="icon-image"></i>', $image->reviewSrc, ['class' => 'lightbox btn btn-icon btn-success'])?>
                                    <?php
                                    if ($image->default_img == 0) {
                                        echo Html::a('<i class="icon-lamp2"></i>', ['/partners/default/default-image', 'id' => $image->image_id], ['class' => 'btn btn-icon btn-success']);
                                    }
                                    ?>
                                    <?= Html::a('<i class="icon-remove"></i>', ['/partners/default/delete-image', 'id' => $image->image_id], ['class' => 'btn btn-icon btn-success']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

            <?php endforeach; ?>
        </div>
    </div>

    <br />

<?php endif; ?>



<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>


<h6 class="heading-hr"><span class="icon-bubble-notification2"></span> Основное</h6>


<?php
if (!$model->isNewRecord) :
    echo \nepster\faceviewer\Widget::widget([
        'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname} <p>ID {id}</p></div>',
        'templateUrl' => ['/users/user/update', 'id' => $model->user->id],
        'data' => [
            'id' => $model->user->id,
            'name' => Html::encode($model->user->profile->name),
            'surname' => $model->user->profile->surname,
            'avatar_url' => $model->user->profile->avatar_url,
        ]
    ]);
endif;
?>

<p><br/></p>



<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'visible')->dropDownList(ArrayHelper::getColumn($model->visibleArray, 'label'))?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'status')->dropDownList(ArrayHelper::getColumn($model->statusArray, 'label')) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'files[]')->fileInput(['class' => 'styled', 'multiple' => 'true']) ?></div>
</div>
<?php if (!$model->isNewRecord) : ?>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'date_create')->textInput(['maxlength' => true, 'disabled' => true]) ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'date_update')->textInput(['maxlength' => true, 'disabled' => true]) ?></div>
    </div>
<?php endif; ?>

<h6 class="heading-hr"><i class="icon-home6"></i> Апартаменты</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'apartment') ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'floor') ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'total_rooms')->dropDownList($model->roomsList) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'total_area') ?></div>
</div>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'remont')->dropDownList($model->remontList) ?></div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'metro_walk')->dropDownList($model->metroWalkList) ?></div>
</div>

<h6 class="heading-hr"><i class="icon-text-width"></i> Дополнительно</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?=
            /*$form->field($model, 'description')->widget(\vova07\imperavi\Widget::className(), [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 200,
                    'imageManagerJson' => Url::to(['/partners/default/images-get']),
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
            ]);*/
            $form->field($model, 'description')->textarea();
        ?>
    </div>
</div>


<h6 class="heading-hr"><i class="icon-arrow-up5"></i> Адрес</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?php
        $city1 = '';
        if (!$model->isNewRecord) {
            if ($model->city) {
                $city1 = $model->city->name;
            }
        }
        ?>
        <?= $form->field($model, 'city_id', [
            'template' => '{label}{input}'.$city1.'{error}',
        ])->textInput([
            'class' => 'form-control select-city', 'data-url' => Yii::$app->params['siteDomain'] . '/geo/select-city'
        ]) ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?= $form->field($model, 'closest_city_id')->textInput([
            'template' => '{label}{input}{error}',
            'class' => 'form-control select-city', 'data-url' => Yii::$app->params['siteDomain'] . '/geo/select-city'
        ]) ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'address') ?></div>
</div>

<br />

<?php if (Yii::$app->user->can('partners-apartment-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

<br />

<?php ActiveForm::end(); ?>