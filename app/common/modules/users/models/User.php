<?php

namespace common\modules\users\models;

use Yii;

/**
 * @inheritdoc
 */
class User extends \nepster\users\models\User
{
    /**
     * @var string
     */
    public $repassword;

    /**
     * @var string
     */
    public $agreement;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'signup' => ['phone', 'email', 'password', 'repassword', 'agreement'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'signup' => self::OP_ALL
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'repassword' => Yii::t('users', 'REPASSWORD'),
            'funds_main' => 'Счет',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'email', 'password', 'repassword', 'agreement'], 'required'],
            [['phone', 'email', 'password', 'repassword'], 'trim'],

            [['phone', 'email'], 'unique'],
            ['phone', \common\validators\PhoneValidator::class, 'message' => 'Некорректный формат номера'],
            ['phone', 'filter', 'filter' => 'intval'],
            ['email', 'email'],

            ['password', \nepster\users\validators\PasswordValidator::class],
            ['repassword', 'compare', 'compareAttribute' => 'password'],

            ['agreement', 'compare', 'compareValue' => '1', 'message' => 'Необходимо согласиться с условиями пользовательского соглашения.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                // Хешируем пароль
                $this->setPassword($this->password);
                // IP пользователя
                if (!Yii::$app instanceof \yii\console\Application) {
                    $this->ip_register = Yii::$app->request->userIP;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            // Сохраняем профиль
            $this->profile->user_id = $this->id;
            $this->profile->phone2 = $this->phone;
            $this->profile->save(false);

            // Сохраняем данные юридического лица
            if ($this->person) {
                $this->person->user_id = $this->id;
                $this->person->save(false);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(LegalPerson::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    /**
     * Данные статуса
     * @param null $status
     * @return array|mixed
     */
    public static function getStatusArray($status = null)
    {
        $statuses = [
            self::STATUS_ACTIVE => Yii::t('users', 'STATUS_ACTIVE'),
            self::STATUS_INACTIVE => Yii::t('users', 'STATUS_INACTIVE'),
            self::STATUS_DELETED => Yii::t('users', 'STATUS_DELETED'),
        ];

        if ($status !== null) {
            return \yii\helpers\ArrayHelper::getValue($statuses, $status);
        }

        return $statuses;
    }

    /**
     * @return array
     */
    public static function getUserTypeArray()
    {
        return Profile::getUserTypeArray();
    }

    /**
     * @return array
     */
    public static function getUserPartnerArray()
    {
        return Profile::getUserPartnerArray();
    }

    /**
     * Быстрая регистрация пользователя
     * @param $data
     * @return bool|integer
     */
    public static function signup($data)
    {
        $user = new User(['scenario' => 'signup']);
        $profile = new Profile(['scenario' => 'signup']);
        $user->setAttributes($data);

        if (isset($data['profile'])) {
            $profile->setAttributes($data['profile']);
        }

        $user->populateRelation('profile', $profile);
        $user->save(false);

        if ($user->save(false)) {
            if (Yii::$app->getModule('users')->requireEmailConfirmation) {
                Yii::$app->consoleRunner->run('users/control/send-email ' . $user->email . ' signup "' . Yii::t('users', 'SUBJECT_SIGNUP') . '"');
            }

            return $user->id;
        }

        return false;
    }
}
