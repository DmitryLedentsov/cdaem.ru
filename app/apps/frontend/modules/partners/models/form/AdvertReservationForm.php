<?php

namespace frontend\modules\partners\models\form;

use Yii;
use common\modules\users\models\User;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models\Calendar;
use frontend\modules\partners\models\AdvertReservation;

/**
 * @inheritdoc
 */
class AdvertReservationForm extends AdvertReservation
{
    /**
     * Тата вьезда
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
     * Пользовательское соглашение
     * @var bool
     */
    public $agreement;

    /**
     * Капча
     * @var string
     */
    public $verifyCode;

    /**
     * Имя формы
     * @return string
     */
    public function formName()
    {
        return 'ReservationForm';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'verifyCode' => 'Защитный код',

            'pets' => 'Животные',
            'children' => 'Дети',

            'clients_count' => 'Кол-во человек',

            'more_info' => 'Пожелания',

            'arrived_date' => 'Дата заезда',
            'arrived_time' => 'Время',

            'out_date' => 'Дата выезда',
            'out_time' => 'Время',

            'actuality_duration' => 'Заявка актуальна',

            'email' => 'EMAIL',
            'phone' => 'Телефон',
            'password' => 'Пароль',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'default' => [
                'pets',
                'children',
                'clients_count',
                'arrived_date',
                'arrived_time',
                'out_date',
                'out_time',
                'actuality_duration',
                'more_info',
            ],
            'unauthorized' => [
                'pets',
                'children',
                'clients_count',
                'arrived_date',
                'arrived_time',
                'out_date',
                'out_time',
                'actuality_duration',
                'more_info',

                'email',
                'phone',
                'password',
                'agreement',
                'verifyCode'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pets', 'children', 'clients_count', 'actuality_duration', 'arrived_date', 'arrived_time', 'out_date', 'out_time'], 'required'],
            [['phone', 'email', 'password', 'agreement'], 'required', 'on' => 'unauthorized'],

            ['pets', 'in', 'range' => array_keys($this->petsArray)],
            ['children', 'in', 'range' => array_keys($this->childrenArray)],
            ['clients_count', 'in', 'range' => array_keys($this->clientsCountArray)],
            ['actuality_duration', 'in', 'range' => array_keys($this->actualityList)],
            ['more_info', 'string', 'max' => 255],
            [['arrived_date', 'out_date'], 'date', 'format' => 'php:d.m.Y'],
            [['arrived_time', 'out_time'], 'date', 'format' => 'php:H:i'],
            ['arrived_date', 'validateAllDates'],
            ['arrived_date', 'validateApartmentAvailable'],

            ['email', 'email'],
            ['password', '\nepster\users\validators\PasswordValidator'],
            ['email', 'unique', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'email'],
            ['phone', 'unique', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'phone'],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],
            ['agreement', 'compare', 'compareValue' => '1', 'message' => 'Необходимо согласиться с условиями пользовательского соглашения.'],

            // Защитный код
            ['verifyCode', 'required', 'message' => 'Подтвердите, что Вы не робот', 'on' => 'unauthorized'],
            ['verifyCode', \common\modules\site\widgets\Captcha::getClassValidator()],
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
     * Валидация незанятости апартамента в даный промежуток времени
     * @param $attribute
     * @param $params
     */
    public function validateApartmentAvailable($attribute, $params)
    {
        // Чтоб не нагружать сервер, проверяем только когда других ошибок нету
        if ($this->hasErrors()) {
            return;
        }

        $apartment_id = Advert::find()->select('apartment_id')->where(['advert_id' => $this->advert_id])->scalar();

        $date_arrived = $this->arrived_date . ' ' . $this->arrived_time;
        $date_out = $this->out_date . ' ' . $this->out_time;

        $date_arrived = \DateTime::createFromFormat('d.m.Y H:i', $date_arrived)->format('Y-m-d H:i:s');
        $date_out = \DateTime::createFromFormat('d.m.Y H:i', $date_out)->format('Y-m-d H:i:s');

        $reserved = Calendar::hasReserved($apartment_id, $date_arrived, $date_out);
        if ($reserved) {
            $this->addError('out_date', 'В данный промежуток времени апартаменты уже зарезервированы');
            $this->addError('arrived_time', '');
            $this->addError('arrived_date', '');
            $this->addError('out_time', '');
        }
    }

    /**
     * Создает новую запись используя эту форму
     * @return bool
     */
    public function process()
    {
        $model = new AdvertReservation;
        $model->user_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : User::signup([
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
        ]);
        $model->landlord_id = $this->landlord_id;
        $model->advert_id = $this->advert_id;
        $model->children = $this->children;
        $model->pets = $this->pets;
        $model->clients_count = $this->clients_count;
        $model->more_info = $this->more_info;

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
