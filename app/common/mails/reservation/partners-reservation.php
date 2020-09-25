<?php

/* @var string common\modules\partners\models\AdvertReservation $reservation */


use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

$domain = Yii::$app->params['siteDomain'];

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

            <p>Вам поступила новая заявка на бронь <b>№<?= $reservation->id ?></b>.</p>
            <p><?= Html::a('Перейдите в личный кабинет, чтобы узнать подробнее...', Url::to(['/office/reservations'], true)) ?></p>

            <br/>
            <p style="text-align: right">С уважением,
                команда <?= str_replace(['http://', 'https://'], '', $domain) ?></p>
        </td>
    </tr>
</table>
