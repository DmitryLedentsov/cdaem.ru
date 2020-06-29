<?php

namespace console\controllers;

use nepster\users\helpers\Security;
use yii\helpers\Console;
use yii\log\Logger;
use Yii;

/**
 * Test Controller
 * @package console\controllers
 */
class TestController extends \yii\console\Controller
{
    /**
     * Отправить письмо
     *
     * @param $setTo
     * @return bool
     */
    public function actionMail($setTo)
    {
        $mail = Yii::$app->getMailer();
        $mail->viewPath = '@common/mails';
        return $mail->compose('test', [])
            ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
            ->setTo($setTo)
            ->setSubject('Тестовое письмо')
            ->send();
    }
}
