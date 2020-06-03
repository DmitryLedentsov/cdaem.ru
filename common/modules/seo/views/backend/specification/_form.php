<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $formModel common\modules\seo\models\backend\SeoSpecificationForm
 * @var $model common\modules\seo\models\SeoSpecification
 */
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

<?php $form = ActiveForm::begin(); ?>

    <h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>
    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
            <?php
            $url = '';
            if ($formModel->scenario == 'update') {
                $url = Yii::$app->params['siteDomain'];
                if ($formModel->city) {
                    $url = str_replace('<city>', $formModel->city, Yii::$app->params['siteSubDomain']);
                }
                $url .= $formModel->url;
                $url = Html::a($url, $url);
            }
            ?>
            <?= $form->field($formModel, 'city', ['template' => "{label}\n{input} " . $url . "\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
            <?= $form->field($formModel, 'url')->textInput(['maxlength' => true]) ?>
        </div>
        <?php if ($formModel->scenario == 'update') :?>
            <div class="row">
                <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
                    <?= $form->field($formModel, 'date_create')->textInput(['maxlength' => true, 'disabled' => true])?>
                </div>
                <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2">
                    <?= $form->field($formModel, 'date_update')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <h6 class="heading-hr"><span class="icon-tags"></span> Мета теги</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-6"><?= $form->field($formModel, 'title')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-6"><?= $form->field($formModel, 'description')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-6 col-lg-6"><?= $form->field($formModel, 'keywords')->textInput(['maxlength' => true]) ?></div>
        </div>
    </div>


    <h6 class="heading-hr"><span class="icon-tags"></span> Служебные контейнеры</h6>
    <div class="block-inner">
        <div class="row">
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-6"><?= $form->field($formModel, 'service_head')->textarea(['maxlength' => true]) ?></div>
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-6"><?= $form->field($formModel, 'service_footer')->textarea(['maxlength' => true]) ?></div>
        </div>
    </div>

    <br />

    <?php if (Yii::$app->user->can('seo-specifications-update')) :?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>

    <br />


<?php ActiveForm::end(); ?>