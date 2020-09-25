<?php

use yii\helpers\Html;

$domain = Yii::$app->params['siteDomain'];
?>

<table class="row" width="100%" bgcolor="#ffffff" align="center" cellpadding="0" cellspacing="0" border="0"
       style="border-collapse:collapse; text-align:left; border-spacing:0; max-width:100%;">
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
        <td class="title" style="padding-top:5px; padding-right:30px; padding-bottom:20px; padding-left:30px; font-family:'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:27px; line-height:36px; font-weight:300;">
            <?= $title ?>
        </td>
    </tr>
    <tr>
        <td style="padding-left:30px; padding-right:30px; padding-bottom:32px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
            <table class="iconContainer" width="130" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0;">
                <tr>
                    <td class="icon" style="padding-top:5px; padding-right:30px;">
                        <div style="font-size:12px; line-height:100%; text-align:center;">
                            <img alt="image" src="<?= $domain ?>/email-images/checkmark_big.png" height="100" width="100" border="0" vspace="0" hspace="0" style="display:block;"/>
                        </div>
                    </td>
                </tr>
            </table>

            <?php
            $fromWhom = $userType == 'renter' ? 'Владельца' : 'Клиента';
            $forWhom = 'Вам или ';
            $forWhom .= $userType == 'renter' ? 'Владельцу' : 'Клиенту';

            ?>

            У <?= $fromWhom ?> есть 24 ч , чтобы оспорить причину «незаезд». Если <?= $secondSide ?> не ответил,значит
            все улуги выполнены
            в полном объёме и стороны не имеют, друг к другу притензий! Компенсация денежных средств за «незаезд»,будет
            произведена
            через 10 дней и пополнит баланс Вашего личного кабинета. Если в течени 24 ч, от <?= $fromWhom ?> поступил
            ответ за
            «незаезд» ,то администрация сайта Сдаём.ру, разбирает данную жалобу двух сторон и принимает решение на своё
            усмотрение, оставляя за собой право - заблокировать личный кабинет, ограничить доступ к аккаунту обоих
            пользователей,
            также посчитать оплаченную услугу выполненной - без возврата денежных средств за услугу, что означает,
            выполнение
            всех денежных операций, выполненными в полном объеме. При положительном решении, <?= $forWhom ?>,
            после 10 дней, будет пополнен баланс личного кабинета, на ту сумму, на которую была произведена оплата за
            услугу.
            Пользователи соглашаются с любым решением и не имеют притензий к «Компании» и ресурсу cdaem.ru!

            <br/>
            <p style="text-align: right">С уважением, команда <?= str_replace(['http://', 'https://'], '', $domain) ?></p>
        </td>
    </tr>
</table>