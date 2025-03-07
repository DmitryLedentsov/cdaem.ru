<?php

namespace common\modules\users\models;

use Yii;

/**
 * Форма авторизации
 * @package common\modules\users\models
 */
class LoginForm extends \nepster\users\models\LoginForm
{
    /**
     * @var \nepster\users\models\User
     */
    protected static $_user;

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'username' => Yii::t('users', 'MAIL_OR_PHONE'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate(): bool
    {
        parent::beforeValidate();

        self::$_user = $this->getUser($this->username);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate(): void
    {
        parent::afterValidate();

        $userId = self::$_user ? self::$_user->id : null;

        // Проверяем заблокирован ли пользователь
        $userBanned = false;
        if ($userBanned = Yii::$app->user->isBanned($userId)) {
            $this->clearErrors();
            $this->addError('username', '');
            $time = Yii::$app->BasisFormat->helper('DateTime')->diffFullPeriod($userBanned['time_banned']);
            if ($userBanned['user_id']) {
                $this->addError('password', Yii::t('users', 'YOU_ACCOUNT_BEEN_BANNED {time} {reason}', [
                    'time' => $userBanned['time_banned'] ? Yii::t('users', 'BEEN_BANNED_TIME {time}', ['time' => $time]) : '',
                    'reason' => $userBanned['reason'] ? Yii::t('users', 'BEEN_BANNED_REASON {reason}', ['reason' => $userBanned['reason']]) : '',
                ]));
            } else {
                $this->addError('password', Yii::t('users', 'YOU_IP_BEEN_BANNED {time} {reason}', [
                    'time' => $userBanned['time_banned'] ? Yii::t('users', 'BEEN_BANNED_TIME {time}', ['time' => $time]) : '',
                    'reason' => $userBanned['reason'] ? Yii::t('users', 'BEEN_BANNED_REASON {reason}', ['reason' => $userBanned['reason']]) : '',
                ]));
            }
        }

        if (!$this->getErrors()) {

            // Записываем попытку успешной авторизации в историю
            Yii::$app->user->action($userId, $this->module->id, 'auth');
        } elseif (!$userBanned) {

            // Записываем попытку неудачной авторизации в историю
            $data = [
                'attributes' => $this->getAttributes(),
                'errors' => $this->getErrors(),
            ];
            Yii::$app->user->action($userId, $this->module->id, 'auth-error', $data);


            // Дополнительные условия авторизации
            // Если условия не выполнены, необходимо заблокировать пользователя
            if (!Yii::$app->user->verifyAuthCondition($userId)) {
                Yii::$app->user->bannedByIp(Yii::$app->request->userIP, time() + $this->module->allowedTimeAttemptsAuth, null);
            }
        }
    }

    /**
     * Поиск пользователя
     * @param $username
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUser($username)
    {
        $scope = $this->scenario == 'admin' ? 'control' : null;

        // Поиск пользователя по телефону
        if (strncasecmp($username, "+", 1) === 0) {
            $username = str_replace('+', '', $username);

            return User::findByPhone($username, [$scope]);
        }

        return User::findByEmail($username, [$scope]);
    }
}
