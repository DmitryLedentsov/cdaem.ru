<?php

namespace console\controllers;

use Yii;

/**
 * Class TestController
 * @package console\controllers
 */
class TestController extends \yii\console\Controller
{
    /**
     * Отправить тестовое письмо
     *
     * @param string $setTo
     */
    public function actionMail(string $setTo): void
    {
        $mail = Yii::$app->getMailer();
        $mail->viewPath = '@common/mails';

        $mail->compose('test', [])
            ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
            ->setTo($setTo)
            ->setSubject('Тестовое письмо')
            ->send();
    }
}
