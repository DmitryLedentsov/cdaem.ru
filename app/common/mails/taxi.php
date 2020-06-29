<?php

/**
 * $name
 * $phone
 * $departureAddress
 * $destinationAddress
 * $carClass
 * $date_delivery
 */

$domain = Yii::$app->params['siteDomain'];

?>

<table class="row" width="100%" bgcolor="#ffffff" align="center" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
    <tr>
        <td style="padding-right:30px; padding-left:30px; border-top:1px #dddddd dotted;">
            <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0;">
                <tr>
                    <td bgcolor="#f4f4f4" style="padding-top:5px; padding-right:5px; padding-bottom:5px; padding-left:5px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#999999;">
                        Такси
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="title" style="padding-top:5px; padding-right:30px; padding-bottom:20px; padding-left:30px; font-family:'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:27px; line-height:36px; color:#666666; font-weight:300;">
            Заказ такси с сайта <?=$domain?>
        </td>
    </tr>
    <tr>
        <td style="padding-left:30px; padding-right:30px; padding-bottom:32px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
            <p><b>Имя</b>: <?=$name?></p>
            <p><b>Телефон</b>: <?=$phone?></p>
            <p><b>Адрес отправления</b>: <?=$departureAddress?></p>
            <p><b>Адрес назначения</b>: <?=$destinationAddress?></p>
            <p><b>Класс автомобиля</b>: <?=$carClass?></p>
            <p><b>Дата и время подачи</b>: <?=Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($date_delivery)?></p>
        </td>
    </tr>
</table>