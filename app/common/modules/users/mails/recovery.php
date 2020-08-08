<?php
/**
 * Activation email view.
 *
 * @var \yii\web\View $this View
 * @var \common\modules\users\models\User $user
 */

use yii\helpers\Url;
use yii\helpers\Html;

$url = Url::toRoute(['/users/guest/recovery-confirmation', 'token' => $user['secure_key']], true);
?>
<p><?= Yii::t('users', 'BODY_RECOVERY_HELLO {name}', ['name' => $user['profile']['name'] . ' ' . $user['profile']['surname']]) ?></p>
<p><?= Yii::t('users', 'BODY_RECOVERY_TOKEN {url}', ['url' => Html::a(Html::encode($url), $url)]) ?></p>