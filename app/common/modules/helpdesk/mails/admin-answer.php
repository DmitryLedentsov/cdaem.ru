<?php

/* @var $model \common\modules\helpdesk\models\HelpdeskAnswers */

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

            <p>Вы получили данное письмо, так как обращались в техническую поддержку:</p>

            <div style="padding: 15px; border: solid 1px #F1F1F1;">
                <h3><?= nl2br(Html::encode($model->helpdesk->theme)); ?></h3>
                <?= nl2br(Html::encode($model->helpdesk->text)); ?>
            </div>

            <div style="padding-left: 55px; margin-top: 25px;">
                <h2>Ответ от оператора</h2>
                <?= nl2br(Html::encode($model->text)); ?>
            </div>

            <br/>
            <p style="text-align: right">С уважением,
                команда <?= str_replace(['http://', 'https://'], '', $domain) ?></p>
        </td>
    </tr>
</table>