<?php
/**
 * Создать пользователя
 * @var yii\base\View $this Представление
 * @var $user common\modules\users\models\backend\User
 * @var $profile common\modules\users\models\Profile
 * @var $person common\modules\users\models\LegalPerson
 */

use yii\helpers\Html;

$this->title = Yii::t('users', 'USER_CREATE');

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Управление пользователями',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все пользователи',
            'url' => ['/users/user/index'],
        ],
        [
            'label' => 'Создать пользователя',
            'url' => ['/users/user/create'],
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
    'user' => $user,
    'profile' => $profile,
    'person' => $person,
]);