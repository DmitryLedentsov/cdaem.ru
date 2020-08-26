<?php

$this->title = 'Редактировать реквизиты';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление реквизитами',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все заявки',
            'url' => ['/agency/details/index'],
        ],
        [
            'label' => 'Управление реквизитами',
            'url' => ['/agency/details/update-files'],
        ],
    ]
]);


use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success indent-bottom">
        <span class="icon-checkmark-circle"></span> <?php echo Yii::$app->session->getFlash('success') ?>
    </div> <br/>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('danger')): ?>
    <div class="alert alert-danger indent-bottom">
        <span class="icon-cancel-circle"></span> <?php echo Yii::$app->session->getFlash('danger') ?>
    </div> <br/>
<?php endif; ?>


<div class="pages-form">

    <?= Html::beginForm('/agency/details/update-files', 'post', ['enctype' => 'multipart/form-data']) ?>

    <div class="form-group">
        <?= Html::label('Альфа-банк') ?>
        <?= Html::textArea('alfabank', $model['alfabank'], ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::label('Легал') ?>
        <?= Html::textArea('legal', $model['legal'], ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::label('Телефон') ?>
        <?= Html::textArea('phone', $model['phone'], ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::label('QIWI') ?>
        <?= Html::textArea('qiwi', $model['qiwi'], ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::label('Сбербанк') ?>
        <?= Html::textArea('sberbank', $model['sberbank'], ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::label('Yamoney') ?>
        <?= Html::textArea('yamoney', $model['yamoney'], ['class' => 'form-control']) ?>
    </div>

    <p><br/></p>

    <div class="form-group">
        <?= Html::submitButton('Редактировать', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php Html::endForm(); ?>

</div>
