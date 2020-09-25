<?php

namespace common\modules\agency\commands;

use Yii;
use common\modules\agency\models\DetailsHistory;

/**
 * Контроллер для работы с реквизитами
 * @package common\modules\agency\commands
 */
class DetailsController extends \yii\console\Controller
{
    /**
     * Отправить реквизиты для оплаты на EMAIL
     * @param $detailId
     */
    public function actionSendEmail($detailId)
    {
        $detail = DetailsHistory::findOne($detailId);
        if ($detail) {
            $subject = 'Реквизиты для оплаты бронирования на сайте - ' . Yii::$app->params['siteDomain'];
            $email = $detail->email;
            $file = Yii::getAlias('@common/modules/agency/details/' . $detail->type . '.txt');
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $data = [
                    'content' => $content,
                ];
                $mail = Yii::$app->getMailer();
                $mail->viewPath = '@common/mails/agency';
                $mail->compose('detail', $data)
                    ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
                    ->setTo($email)
                    ->setSubject($subject)
                    ->send();
                Yii::info('SUCCESS: Письмо успешно отправлено на почтовый адрес: ' . $email, 'agency-details');
            } else {
                Yii::error('ERROR: файл шаблона "' . $detail->type . '" не найден', 'agency-details');
            }
        } else {
            Yii::error('ERROR: Заявка на реквизиты №' . $detailId . ' не найдена в базе данных', 'agency-details');
        }
    }
}
