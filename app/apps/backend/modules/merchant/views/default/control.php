<?php

/**
 * Управление счетом
 * @var yii\base\View $this Представление
 * @var $searchModel backend\modules\merchant\models\PaymentSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = 'Управление счетом';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Мерчант',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Мерчант',
            'url' => ['/merchant/default/index'],
        ],
        [
            'label' => 'Управление счетом',
        ]
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

<br />


<h2>Управление счетом пользователя:</h2>

<h6 class="heading-hr"><span class="icon-user"></span> Пользователь</h6>
<?php
    echo \nepster\faceviewer\Widget::widget([
        'template' => '<div class="user-face"><div class="avatar">{face}</div>{name} {surname} <p>{funds}</p></div>',
        'templateUrl' => ['/users/user/update', 'id' => $user->id],
        'data' => [
            'name' => Html::encode($user->profile->name),
            'surname' => Html::encode($user->profile->surname),
            'avatar_url' => $user->profile->avatar_url,
            'funds' => Yii::$app->formatter->asCurrency($user->funds_main, 'rub'),
        ]
    ]);
?>

<p><br /></p>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'type')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($model->typeArray)) ?></div>
</div>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'system')->dropDownList(Yii::$app->BasisFormat->helper('Status')->getList($systemsArray), ['options' => [
            'system' => [
                'selected ' => 'selected',
            ]
        ]]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-1 col-md-2 col-sm-2 col-xs-6 col-lg-2"><?= $form->field($model, 'funds') ?></div>
</div>
<br />

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
</div>

<br />

<?php ActiveForm::end(); ?>
