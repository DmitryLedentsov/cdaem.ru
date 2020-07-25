<?php

/* @var string $userType Владелец или клиент */
/* @var string $type Подтверждение или отмена */

/* @var string common\modules\partners\models\AdvertReservation $reservation */


use yii\helpers\Json;
use yii\helpers\Html;

$domain = Yii::$app->params['siteDomain'];

$userTypeText = ($userType == 'owner') ? 'Владелец' : 'Клиент';
$sub = $userTypeText . ' подтвердил заявку на бронь №' . $reservation->id;

$timeIntervalPaymentReservation = (Yii::$app->getModule('partners')->timeIntervalPaymentReservation / 60 / 60);
$timeIntervalPaymentReservation = $timeIntervalPaymentReservation . ' часов';

?>

<table class="row" width="100%" bgcolor="#ffffff" align="center" cellpadding="0" cellspacing="0" border="0"
       style="border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
    <tr>
        <td style="padding-right:30px; padding-left:30px; border-top:1px #dddddd dotted;">
            <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0;">
                <tr>
                    <td bgcolor="#f4f4f4"
                        style="padding-top:5px; padding-right:5px; padding-bottom:5px; padding-left:5px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#999999;">
                        Информация
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="title"
            style="padding-top:5px; padding-right:30px; padding-bottom:20px; padding-left:30px; font-family:'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:27px; line-height:36px; color: green; font-weight:300;">
            <?= $sub ?>
        </td>
    </tr>
    <tr>
        <td style="padding-left:30px; padding-right:30px; padding-bottom:32px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
            <table class="iconContainer" width="130" align="left" cellpadding="0" cellspacing="0"
                   style="border-collapse:collapse; border-spacing:0;">
                <tr>
                    <td class="icon" style="padding-top:5px; padding-right:30px;">
                        <div style="font-size:12px; line-height:100%; text-align:center;">
                            <img alt="image" src="<?= $domain ?>/email-images/checkmark_big.png" height="100"
                                 width="100" border="0" vspace="0" hspace="0" style="display:block;"/>
                        </div>
                    </td>
                </tr>
            </table>

            <?php if ($userType == 'owner'): ?>
                <p>
                    Владелец апартаментов на сайте <?= $domain ?> успешно подтвердил <br/> заявку на бронь
                    <b>№<?= $reservation->id ?></b>.<br/>
                    Теперь Вам необходимо подтвердить заявку в течении <b><?= $timeIntervalPaymentReservation ?></b>
                    для завершения сделки.
                </p>
            <?php else: ?>
                <p>
                    Клиент на сайте <?= $domain ?> успешно подтвердил <br/> заявку на бронь
                    <b>№<?= $reservation->id ?></b>.<br/>
                    Теперь Вам необходимо подтвердить заявку в течении <b><?= $timeIntervalPaymentReservation ?></b>
                    для завершения сделки.
                </p>
            <?php endif; ?>

            <br/>
            <p style="text-align: right">С уважением,
                команда <?= str_replace(['http://', 'https://'], '', $domain) ?></p>
        </td>
    </tr>
</table>