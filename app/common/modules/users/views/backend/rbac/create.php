<?php
/**
 * Создать группу
 * @var yii\base\View $this Представление
 * @var $model nepster\users\rbac\models\AuthItem
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('users.rbac', 'GROUP_CREATE');

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление пользователями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все пользователи',
            'url' => ['/users/user/index'],
        ],
        [
            'label' => 'Создать группу',
            'url' => ['/users/rbac/create'],
        ]
    ]
]);
?>

<?php if (Yii::$app->session->hasFlash('danger')): ?>
    <div class="alert alert-danger indent-bottom">
        <?php echo Yii::$app->session->getFlash('danger') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success indent-bottom">
        <?php echo Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

    <p><br/></p>

<?php
echo $this->render('_form', [
    'model' => $model,
]);
