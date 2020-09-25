<?php

namespace common\modules\reviews\models;

use yii;
use common\modules\users\models\User;

/**
 * Форма "Написать отзыв"
 * @package common\modules\reviews\models
 */
class ReviewForm extends Review
{
    /**
     * EMAIL
     * @var string
     */
    public $email;

    /**
     * Телефон
     * @var string
     */
    public $phone;

    /**
     * Пароль
     * @var string
     */
    public $password;

    /**
     * Пользовательское соглашение
     * @var bool
     */
    public $agreement;

    /**
     * Имя
     * @var string
     */
    //public $name;

    /**
     * Фамилия
     * @var string
     */
    //public $surname;

    /**
     * Защитный код
     * @var string
     */
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'date_create',
                ],
                "value" => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'verifyCode' => 'Защитный код',
            'email' => 'EMAIL',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            /*'name' => 'Имя',
            'surname' => 'Фамилия',*/
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'user-create' => ['apartment_id', 'match_description', 'price_quality', 'cleanliness', 'entry', 'text'],
            'guest-create' => ['apartment_id', 'match_description', 'price_quality', 'cleanliness', 'entry', 'text', 'verifyCode', 'email', 'phone', 'password', 'agreement', /*'name', 'surname'*/],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'user-create' => self::OP_ALL,
            'guest-create' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Защитный код
            ['verifyCode', 'required', 'on' => 'guest-create'],
            ['verifyCode', 'captcha', 'captchaAction' => '/site/default/captcha'],

            ['apartment_id', 'required'],
            ['apartment_id', 'exist', 'targetClass' => '\common\modules\partners\models\Apartment', 'targetAttribute' => 'apartment_id'],

            // Email
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'email'],

            // Телефон
            ['phone', 'required'],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],
            ['phone', 'unique', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'phone'],

            // Пароль пользователя
            ['password', 'required'],
            ['password', '\nepster\users\validators\PasswordValidator'],

            // Отзыв
            ['text', 'required'],
            ['text', 'string', 'min' => 20],

            // Очки рейтинга
            [['match_description', 'price_quality', 'cleanliness', 'entry'], 'required'],
            ['match_description', 'in', 'range' => array_keys(self::getRatingMatchDescriptionArray())],
            ['price_quality', 'in', 'range' => array_keys(self::getRatingPriceAndQualityArray())],
            ['cleanliness', 'in', 'range' => array_keys(self::getRatingCleanlinessArray())],
            ['entry', 'in', 'range' => array_keys(self::getRatingEntryArray())],

            // Пользовательское соглашение
            ['agreement', 'required'],
            ['agreement', 'compare', 'compareValue' => '1', 'message' => 'Необходимо согласиться с условиями пользовательского соглашения.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->scenario == 'guest-create') {

            // Регистрация пользователя
            $this->user_id = User::signup([
                'email' => $this->email,
                'password' => $this->password,
                'phone' => $this->phone,
            ]);
        } else {
            $this->user_id = Yii::$app->user->id;
        }

        $this->moderation = 0;

        return true;
    }
}
