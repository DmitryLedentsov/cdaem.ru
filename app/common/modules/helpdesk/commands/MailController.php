<?php

namespace common\modules\helpdesk\commands;

use Yii;
use yii\log\Logger;
use yii\helpers\Console;
use common\modules\helpdesk\models\HelpdeskAnswers;

/**
 * Mail Controller
 * @package nepster\users\commands
 */
class MailController extends \yii\console\Controller
{
    /**
     * @var string
     */
    public $mailViewPath = '@common/modules/helpdesk/mails/';

    /**
     * Admin Answer
     *
     * @param $answerId
     *
     * Command: php yii helpdesk/email/admin-answer [answerId]
     */
    public function actionAdminAnswer($answerId)
    {
        $model = HelpdeskAnswers::find()
            ->where('answer_id = :answer_id', [':answer_id' => $answerId])
            ->joinWith('helpdesk')
            ->one();

        if ($model) {
            if ($model->helpdesk->email) {
                $email = $model->helpdesk->email;
            } else {
                $email = $model->helpdesk->user->email;
            }

            $subject = 'Ответ оператора технической поддержки';
            $content = $model->text;

            if ($this->sendMail('admin-answer', $subject, $email, ['model' => $model])) {
                $this->stdout("SUCCESS" . PHP_EOL, Console::FG_GREEN);
                Yii::getLogger()->log('SUCCESS: E-MAIL: ' . $email . ', ANSWER_ID: ' . $answerId, Logger::LEVEL_INFO, 'users.send');
            } else {
                $this->stdout("ERROR" . PHP_EOL, Console::FG_RED);
                Yii::getLogger()->log('ERROR: E-MAIL: ' . $email . ', ANSWER_ID: ' . $answerId, Logger::LEVEL_ERROR, 'users.send');
            }
        } else {
            Yii::getLogger()->log('ERROR: send fail. ANSWER_ID: ' . $answerId, Logger::LEVEL_ERROR, 'users.send');
        }
    }

    /**
     * @param $view
     * @param $subject
     * @param $email
     * @param array $data
     * @return bool
     */
    private function sendMail($view, $subject, $email, array $data)
    {
        $mail = Yii::$app->getMailer();
        $mail->viewPath = $this->mailViewPath;

        return $mail->compose($view, $data)
            ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
            ->setTo($email)
            ->setSubject($subject)
            ->send();
    }
}
