<?php
/* @var $this yii\web\View */
/* @var array $models */
/* @var array $advertisements */

/* @var array $selected */

use yii\helpers\Url;
use yii\helpers\Html;

\common\modules\agency\assets\backend\AgencyAsset::register($this);

$this->title = 'Создать несколько рекламных объявлений';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление рекламными объявлениями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все рекламные объявления',
            'url' => ['/agency/advertisement/index'],
        ],
        [
            'label' => 'Создать',
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

    <div id="advertisement-multi-create-apartments-list">
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
                        <?php
                        if (isset($advertisements[$model->apartment_id]) && !empty($advertisements[$model->apartment_id])) {
                            foreach ($advertisements[$model->apartment_id] as $advertisement) {
                                $checkbox = Html::checkbox('selected[' . $advertisement->advert_id . ']', in_array($advertisement->advert_id, array_keys($selected)), [
                                    'label' => $advertisement->advert->rentType->name,
                                ]);

                                $form = $this->render('_multi_form.php', [
                                    'model' => $advertisement,
                                ]);

                                $form = Html::tag('div', $form, [
                                    'style' => 'display: none',
                                    'class' => 'rent-type-form',
                                ]);

                                echo Html::tag('div', Html::tag('h4', $checkbox) . $form, [
                                    'class' => 'rent-type',
                                    'style' => 'border: solid 1px silver; padding: 10px 10px 5px 10px; margin-bottom: 25px;',
                                    'data' => [
                                        'rent_type_id' => $advertisement->advert->rentType->rent_type_id,
                                    ]
                                ]);
                            }
                        } else {
                            echo Html::tag('p', 'Нет объявлений для рекламы. <a href="' . Url::toRoute(['/agency/advertisement/index', 's[advert.apartment.apartment_id]' => $model->apartment_id]) . '" target="_blank">Посмотреть все рекламные объявления.</a>');
                        }
                        ?>
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