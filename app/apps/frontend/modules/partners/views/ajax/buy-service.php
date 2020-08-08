<?php
/**
 * Оплата покупки сервиса
 * @var \yii\web\View this
 * @var Yii::$app->service->load($service) $service
 * @var array $selected
 * @var array $calculation
 * @var int $processId
 * @var datetime $date
 */

use yii\helpers\Html;
use common\modules\partners\models\Service;
use frontend\modules\merchant\widgets\fastpayment\FastPayment;

$timeActivationWord = $date;

if ($date == date('d.m.Y')) {
    $timeActivationWord = 'в течении нескольких минут';
}

$msg = '<p><strong>Оплата активации услуги.</strong></p>';
$msg .= '<p>Ваша услуга будет активирована ' . $timeActivationWord . '. ';
$msg .= 'После оплаты Вам будет отправлено письмо на Ваш почтовый адрес с подробной информацией.</p>';

echo FastPayment::widget([
    'id' => 'open-user-contacts',
    'description' => $msg,
    'processServiceId' => $processId
]);
