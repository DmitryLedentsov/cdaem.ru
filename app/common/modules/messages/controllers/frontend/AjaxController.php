<?php

namespace common\modules\messages\controllers\frontend;

use Yii;
use yii\web\Response;
use common\modules\messages\models\Mailbox;
use common\modules\messages\models\Message;

/**
 * Class AjaxController
 * @package common\modules\messages\controllers\frontend
 */
class AjaxController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            if (!Yii::$app->request->isAjax) {
                return $this->goBack();
            }

            $this->module->viewPath = '@common/modules/messages/views/frontend';

            return true;
        }

        return false;
    }

    /**
     * Возвращает данные для написания сообщения
     * @return string|Response
     */
    public function actionMessage($interlocutorId)
    {
        Yii::$app->response->format = Response::FORMAT_HTML;

        if (Yii::$app->user->isGuest) {
            /*$user = new User(['scenario' => 'signup']);
            $profile = new Profile(['scenario' => 'create']);*/
            return $this->renderAjax('signup.php', [
                /*'user' => $user,
                'profile' => $profile,
                'interlocutorId' => $interlocutorId*/
            ]);
        }

        $message = new Message(['scenario' => 'create']);

        return $this->renderAjax('message.php', [
            'message' => $message,
            'interlocutorId' => $interlocutorId
        ]);
    }

    /**
     * Удаление одного сообщения
     * @param $messageId
     * @return array
     */
    public function actionDeleteMessage($messageId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $mailbox = Mailbox::find()->where(['message_id' => $messageId])->thisUser()->one();
        if ($mailbox && $mailbox->deleteMessage()) {
            return [
                'status' => 1,
                'message' => 'Сообщение удалено'
            ];
        }

        return [
            'status' => 0,
            'message' => 'Возникла критическая ошибка'
        ];
    }

    /**
     * @param $interlocutorId
     * @return array
     */
    public function actionDeleteConversation($interlocutorId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $count = Mailbox::updateAll(
            ['deleted' => 1],
            [
                'user_id' => Yii::$app->user->id,
                'interlocutor_id' => $interlocutorId
            ]
        );

        if ($count) {
            return [
                'status' => 1,
                'message' => 'Сообщения удалены'
            ];
        }

        return [
            'status' => 0,
            'message' => 'Возникла критическая ошибка'
        ];
    }
}
