<?php

/* @var $this yii\web\View */

/* @var array $models */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

\common\modules\agency\assets\backend\AgencyAsset::register($this);

$this->title = 'Редактировать объявления';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление апартаментами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все апартаменты',
            'url' => ['/agency/default/index'],
        ],

        [
            'label' => 'Редактировать объявления',
        ]
    ]
]);

?>

<?php if (Yii::$app->session->hasFlash('info')): ?>
    <div class="alert alert-info indent-bottom">
        <span class="icon-info"></span> &nbsp; <?php echo Yii::$app->session->getFlash('info') ?>
    </div>
<?php endif; ?>

    <p><br/></p>


<?php echo Html::beginForm(); ?>

    <div id="adverts-multi-update-rent-types-list">
        <h3>Выберите типы аренды для работы: </h3>
        <?php
        echo Html::checkboxList('rent-types-list', array_keys($rentTypesList), $rentTypesList);
        ?>
    </div>

    <p><br/></p>

    <div id="adverts-multi-update-apartments-list">
        <div class="apartments">
            <?php foreach ($models as $model): ?>

                <?php echo Html::tag('h1', $model->address); ?>

                <div class="row">
                    <div class="col-md-4 col-md-4 col-sm-4 col-xs-12 col-lg-2">
                        <a href="<?php echo Url::to(['/agency/default/update', 'id' => $model->apartment_id]) ?>"
                           target="_blank">
                            <div class="thumbnail thumbnail-boxed">
                                <div class="thumb">
                                    <?php echo Html::img($model->titleImageSrc) ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-8 col-md-8 col-sm-8 col-xs-12 col-lg-10">
                        <?php foreach ($model->adverts as $advert): ?>
                            <?php
                            $form = $this->render('_multi_form.php', [
                                'model' => $advert,
                            ]);

                            $form = Html::tag('div', $form, [
                                'style' => 'display: none',
                                'class' => 'rent-type-form',
                            ]);

                            echo Html::tag('div', Html::tag('h4', $advert->rentType->name, ['style' => 'cursor:pointer']) . $form, [
                                'class' => 'rent-type',
                                'style' => 'border: solid 1px silver; padding: 10px 10px 5px 10px; margin-bottom: 25px;',
                                'data' => [
                                    'rent_type_id' => $advert->rentType->rent_type_id
                                ]
                            ]);
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php echo Html::tag('hr'); ?>
                <?php echo Html::tag('p', '<br/>'); ?>

            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

<?php echo Html::endForm(); ?>