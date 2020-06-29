<?php

/* @var Yii::$app->service $service */
/* @var \common\modules\users\models\User $user */
/* @var \frontend\modules\partners\models\AdvertReservation or \frontend\modules\partners\models\Reservation $reservation */

$domain = Yii::$app->params['siteDomain'];

use common\modules\users\helpers\UserHelper;
use yii\helpers\Html;
?>

<table class="row" width="100%" bgcolor="#ffffff" align="center" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
    <tr>
        <td style="padding-right:30px; padding-left:30px; border-top:1px #dddddd dotted;">
            <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0;">
                <tr>
                    <td bgcolor="#f4f4f4" style="padding-top:5px; padding-right:5px; padding-bottom:5px; padding-left:5px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#999999;">
                        Информация
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="title" style="padding-top:5px; padding-right:30px; padding-bottom:20px; padding-left:30px; font-family:'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:27px; line-height:36px; color:#666666; font-weight:300;">
            Услуга "<?=$service->getName()?>"
        </td>
    </tr>
    <tr>
        <td style="padding-left:30px; padding-right:30px; padding-bottom:32px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">

            <table class="iconContainer" width="130" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0;">
                <tr>
                    <td class="icon" style="padding-top:5px; padding-right:30px;">
                        <div style="font-size:12px; line-height:100%; text-align:center;"><img alt="image" src="<?=$domain?>/email-images/checkmark_big.png" height="100" width="100" border="0" vspace="0" hspace="0" style="display:block;" /></div>
                    </td>
                </tr>
            </table>


            <p><b>Здравствуйте!</b></p>


            <p>Вы получили это письмо, так как запросили контактные данные клиента в заявке <b>№<?=$reservation->id?></b></p>

            <br/>

            <h2>Контактные данные указанные пользователем:</h2>
            <div style="font-size: 16px">
                <?=UserHelper::getOpenContactsBlock($reservation->user) ?>
            </div>

            <br/>

            <p style="text-align: right">С уважением, команда cdaem.ru</p>

        </td>
    </tr>
</table>