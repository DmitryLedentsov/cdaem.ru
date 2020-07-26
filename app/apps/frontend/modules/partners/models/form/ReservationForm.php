<?php

namespace frontend\modules\partners\models\form;

use frontend\modules\partners\models\Reservation;
use yii\behaviors\TimestampBehavior;
use common\modules\users\models\User;
use common\modules\users\models\Profile;
use Yii;

/**
 * @inheritdoc
 */
class ReservationForm extends Reservation
{
    /**
     * Дата вьезда
     * @var string
     */
    public $arrived_date;

    /**
     * Время вьезда
     * @var string
     */
    public $arrived_time;

    /**
     * Дата сьезда
     * @var string
     */
    public $out_date;

    /**
     * Время сьезда
     * @var string
     */
    public $out_time;

    /**
     * Время актуальности заявки
     * @var
     */
    public $actuality_duration;

    /**
     * Капча
     * @var string
     */
    public $verifyCode;

    /**
     * Имя
     * @var string
     */
    public $name;

    /**
     * Емейл
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
     * Планируемый бюджет
     * @var
     */
    public $budget;

    /**
     * Пользовательское соглашение
     * @var bool
     */
    public $agreement;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'date_update',
                ],
                'value' => function ($event) {
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

            'city' => 'Город',

            'arrived_date' => 'Дата заезда',
            'arrived_time' => 'Время',

            'out_date' => 'Дата выезда',
            'out_time' => 'Время',

            'actuality_duration' => 'Заявка актуальна',

            'budget' => 'Планируемый бюджет',

            'email' => 'EMAIL',
            'phone' => 'Телефон',
            'password' => 'Пароль',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'unauthorized' => self::OP_ALL,
            'default' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'unauthorized' => [
                'city_id',
                'address',
                'beds',
                'pets',
                'children',
                'floor',
                'clients_count',
                'arrived_date',
                'arrived_time',
                'metro_walk',
                'out_date',
                'out_time',
                'rent_type',
                'rooms',
                'more_info',
                'verifyCode',
                'actuality_duration',
                'money_from',
                'money_to',
                'currency',

                'email',
                'phone',
                'password',
                'agreement',
            ],

            'default' => [
                'city_id',
                'address',
                'beds',
                'pets',
                'children',
                'floor',
                'clients_count',
                'arrived_date',
                'arrived_time',
                'metro_walk',
                'out_date',
                'out_time',
                'rent_type',
                'rooms',
                'more_info',
                'actuality_duration',
                'money_from',
                'money_to',
                'currency',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['beds', 'pets', 'children', 'floor', 'clients_count', 'phone', 'email', 'rent_type', 'rooms', 'city_id', 'address', 'arrived_date', 'arrived_time', 'out_date', 'out_time', 'actuality_duration'], 'required'],
            [['money_from', 'money_to'], 'required', 'message' => 'Некорректно указан диапазон значений суммы'],
            [['email', 'phone', 'password', 'agreement'], 'required', 'on' => 'unauthorized'],

            [['arrived_date', 'out_date'], 'date', 'format' => 'php:d.m.Y'],
            [['arrived_time', 'out_time'], 'date', 'format' => 'php:H:i'],
            ['arrived_date', 'validateAllDates'],

            [['address', 'more_info'], 'string', 'max' => 255],

            ['email', 'email'],
            ['password', '\nepster\users\validators\PasswordValidator'],
            ['email', 'unique', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'email'],
            ['phone', 'unique', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'phone'],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],

            ['city_id', 'exist', 'targetClass' => '\common\modules\geo\models\City', 'targetAttribute' => 'city_id'],

            [['money_from', 'money_to'], 'integer', 'min' => 1, 'max' => 1000000,
                'tooBig' => 'Диапазон значений слишком большой',
                'tooSmall' => 'Диапазон значений слишком маленький',
                'message' => 'Некорректно указан диапазон значений суммы'],
            ['money_to', 'compare', 'operator' => '>', 'compareAttribute' => 'money_from',
                'message' => 'Некорректно указан диапазон значений суммы'],
            ['money_from', 'validateBudget', 'skipOnEmpty' => false, 'skipOnError' => false],

            ['currency', 'in', 'range' => array_keys($this->currencyArray), 'message' => 'Некорректно указана валюта'],
            ['rent_type', 'in', 'range' => array_keys($this->rentTypesList)],
            ['rooms', 'in', 'range' => array_keys($this->roomsList)],
            ['floor', 'in', 'range' => array_keys($this->floorArray)],
            ['beds', 'in', 'range' => array_keys($this->bedsList)],
            ['children', 'in', 'range' => array_keys($this->childrenArray)],
            ['pets', 'in', 'range' => array_keys($this->petsArray)],
            ['clients_count', 'in', 'range' => array_keys($this->clientsCountArray)],
            ['metro_walk', 'in', 'range' => array_keys($this->metroWalkList)],
            ['actuality_duration', 'in', 'range' => array_keys($this->actualityList)],

            ['verifyCode', 'required', 'message' => 'Подтвердите, что Вы не робот'],
            ['verifyCode', \frontend\modules\site\widgets\Captcha::getClassValidator()],

            ['agreement', 'compare', 'compareValue' => '1', 'message' => 'Необходимо согласиться с условиями пользовательского соглашения.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->phone = str_replace(['(', ')', '+', ' ', '-'], '', $this->phone);

        return true;
    }

    /**
     * Валидация Даты заезда и даты съезда в одном валидаторе
     *
     * Валидирует все 4 поля (arrived_date, arrived_time, out_date, out_time)
     * ВНИМАНИЕ: для нажежности приложения, этот валидатор стоит вызивать уже после валидаторов "required" и "format",
     * при чем в такой же последовательности
     * @param $attribute
     * @param $params
     */
    public function validateAllDates($attribute, $params)
    {
        // На данном этапе валидации, уже должны быть провалидированы все 4 поля валидаторами "required" и "format"
        if ($this->hasErrors('arrived_date') or $this->hasErrors('arrived_time') or $this->hasErrors('out_date') or $this->hasErrors('out_time')) {
            return;
        }

        $timeNow = time();
        $timeArrived = strtotime($this->arrived_date . ' ' . $this->arrived_time);
        $timeOut = strtotime($this->out_date . ' ' . $this->out_time);

        // 1. Бронирование апартаментов не ранее чем за час
        if (($timeArrived - $timeNow) < 3600) {
            $this->addError('arrived_date', '');
            $this->addError('arrived_time', 'Необходимо бронировать не менее чем за час');
        }

        // 2. Дата съезда должна быть больше даты въезда минимум на 2 часа
        if (($timeOut - $timeArrived) < 7200) {
            $this->addError('out_date', '');
            $this->addError('out_time', 'Минимальное время аренды 2 часа');
        }
    }

    /**
     * Перенаправление ошибок атрибутов "money_from" и "money_to" на атрибут "budget"
     * @param $attribute
     * @param $params
     */
    public function validateBudget($attribute, $params)
    {
        if ($this->getErrors('money_from')) {
            $this->addError('budget', $this->getFirstError('money_from'));
            $this->clearErrors('money_from');
        } else if ($this->getErrors('money_to')) {
            $this->addError('budget', $this->getFirstError('money_to'));
            $this->clearErrors('money_to');
        }
    }

    /**
     * Создает новую запись используя эту форму
     * @return bool
     */
    public function process()
    {
        $model = new Reservation;
        $model->user_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : User::signup([
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
        ]);
        $model->rent_type = $this->rent_type;
        $model->city_id = $this->city_id;
        $model->address = $this->address;
        $model->children = $this->children;
        $model->pets = $this->pets;
        $model->clients_count = $this->clients_count;
        $model->more_info = $this->more_info;
        $model->money_from = $this->money_from;
        $model->money_to = $this->money_to;
        $model->currency = $this->currency;
        $model->rooms = $this->rooms;
        $model->beds = $this->beds;
        $model->floor = $this->floor;
        $model->metro_walk = $this->metro_walk;

        $date_arrived = $this->arrived_date . ' ' . $this->arrived_time;
        $date_out = $this->out_date . ' ' . $this->out_time;
        $model->date_arrived = \DateTime::createFromFormat('d.m.Y H:i', $date_arrived)->format('Y-m-d H:i:s');
        $model->date_out = \DateTime::createFromFormat('d.m.Y H:i', $date_out)->format('Y-m-d H:i:s');

        $now = new \DateTime('now');
        $model->date_create = $now->format('Y-m-d H:i:s');
        $model->date_update = $model->date_create;

        switch ($this->actuality_duration) {
            case 1:
                $now->add(new \DateInterval('PT15M'));
                $model->date_actuality = $now->format('Y-m-d H:i:s');
                break;
            case 2:
                $now->add(new \DateInterval('PT30M'));
                $model->date_actuality = $now->format('Y-m-d H:i:s');
                break;
            case 3:
                $now->add(new \DateInterval('PT1H'));
                $model->date_actuality = $now->format('Y-m-d H:i:s');
                break;
            case 4:
                $now->add(new \DateInterval('P1D'));
                $model->date_actuality = $now->format('Y-m-d H:i:s');
                break;
            case 5:
                $model->date_actuality = $model->date_arrived;
                break;
        }

        if ($model->save(false)) {
            $this->id = $model->id;
            return true;
        }

        return false;
    }
}
