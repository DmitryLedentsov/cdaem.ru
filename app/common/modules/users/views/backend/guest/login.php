<?php
/**
 * Авторизация
 * @var yii\base\View $this View
 * @var \common\modules\users\models\LoginForm model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\backend\themes\basic\assets\FormsAsset::register($this);

$this->title = Yii::t('users', 'SIGNIN.TITLE');
$this->context->layout = '//min';
?>

<!-- Login wrapper -->
<div class="login-wrapper">
    <?php $form = ActiveForm::begin(['enableAjaxValidation' => false, 'id' => 'form-login']); ?>
    <div class="popup-header">
        <span class="text-semibold"><?php echo Yii::t('users', 'SIGNIN.TITLE') ?></span>
    </div>

    <div class="well">
        <?= $form->beginField($model, 'username', ['options' => ['class' => 'has-feedback']]) ?>
        <?= Html::activeLabel($model, 'username') ?>
        <?= Html::activeTextInput($model, 'username', ['class' => 'form-control']) ?><i
                class="icon-users form-control-feedback"></i>
        <?= Html::error($model, 'username', ['class' => 'help-block']) ?>
        <?= $form->endField() ?>

        <?= $form->beginField($model, 'password', ['options' => ['class' => 'has-feedback']]) ?>
        <?= Html::activeLabel($model, 'password') ?>
        <?= Html::activePasswordInput($model, 'password', ['class' => 'form-control']) ?><i
                class="icon-lock form-control-feedback"></i>
        <?= Html::error($model, 'password', ['class' => 'help-block']) ?>
        <?= $form->endField() ?>

        <div class="row form-actions">
            <div class="col-xs-6">
                <div class="checkbox checkbox-success"><?= $form->field($model, 'rememberMe')->checkbox(['class' => 'styled']) ?></div>
            </div>
            <div class="col-xs-6"><?= Html::submitButton('<i class="icon-menu2"></i>' . Yii::t('users', 'SEND'), ['class' => 'btn btn-warning pull-right']) ?></div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<!-- /login wrapper -->
