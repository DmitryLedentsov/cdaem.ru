<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\agency\models\Image */
/* @var $formModel common\modules\agency\models\backend\form\ImageForm */

\common\modules\agency\assets\backend\ImageFormAsset::register($this);
?>

<?php if (!Yii::$app->request->isAjax): ?>

    <?php
    $this->title = 'Редактировать метатеги картинки';

    echo \backend\modules\admin\widgets\HeaderWidget::widget([
        'title' => 'Метатеги картинки',
        'description' => $this->title,
        'breadcrumb' => [
            [
                'label' => 'Все апартаменты',
                'url' => ['/agency/default/index'],
            ],
            [
                'label' => 'Апартамент № ' . $model->apartment_id,
                'url' => ['/agency/default/update', 'id' => $model->apartment_id],
            ],
            [
                'label' => 'Редактировать',
                'url' => ['/agency/default/update-image', 'id' => $model->image_id],
            ],
        ]
    ]);
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

<?php endif; ?>

<br/>

<?= ((Yii::$app->request->isAjax) ? '<div class="page-content">' : '') ?>

<?php $form = ActiveForm::begin(['id' => 'image_form']); ?>

<h6 class="heading-hr"><span class="icon-text-width"></span> Метатеги</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-6"><?= $form->field($formModel, 'alt')->textInput(['maxlength' => true]) ?></div>
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-6"><?= $form->field($formModel, 'title')->textInput(['maxlength' => true]) ?></div>
</div>

<br/>

<?php if (Yii::$app->user->can('agency-apartment-update')) : ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

<br/>

<?php ActiveForm::end(); ?>

<?= ((Yii::$app->request->isAjax) ? '</div>' : '') ?>

