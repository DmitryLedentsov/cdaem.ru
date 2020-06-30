<?php

namespace frontend\modules\site\models;

use Yii;

/**
 * Заказ такси
 * @package frontend\modules\site\models
 */
class Taxi extends \yii\base\Model
{
    const CAR_ECONOMY = 'economy';
    const CAR_BUSINESS = 'business';
    const CAR_VIP = 'vip';

    /**
     * Имя
     * @var string
     */
    public $name;

    /**
     * Телефон
     * @var string
     */
    public $phone;

    /**
     * Адрес отправления
     * @var string
     */
    public $departureAddress;

    /**
     * Адрес назначения
     * @var string
     */
    public $destinationAddress;

    /**
     * Класс автомобиля
     * @var string
     */
    public $carClass;

    /**
     * Дата подачи
     * @var string
     */
    public $date;

    /**
     * Время подачи
     * @var string
     */
    public $time;

    /**
     * Дата и время подачи
     * @var string
     */
    public $date_delivery;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
            'departureAddress' => 'Адрес отправления',
            'destinationAddress' => 'Адрес назначения',
            'carClass' => 'Класс автомобиля',
            'date' => 'Дата подачи',
            'time' => 'Время подачи',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],

            ['phone', 'required'],
            ['phone', 'number'],

            ['departureAddress', 'required'],
            ['departureAddress', 'string', 'max' => 200],

            ['destinationAddress', 'required'],
            ['destinationAddress', 'string', 'max' => 200],

            ['carClass', 'required'],
            ['carClass', 'in', 'range' => array_keys(self::getCarClassArray())],

            ['date', 'required'],

            ['time', 'required'],
        ];
    }

    /**
     * Классы автомобиля
     * @return array
     */
    public function getCarClassArray()
    {
        return [
            self::CAR_ECONOMY => 'Эконом класс',
            self::CAR_BUSINESS => 'Бизнес класс',
            self::CAR_VIP => 'VIP класс',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            $this->phone = str_replace(['(', ')', '+', ' ', '-'], '', $this->phone);

            $validator = new \yii\validators\DateValidator();
            $validator->format = 'php:d.m.Y H:i';
            $date_delivery = trim($this->date . ' ' . $this->time);
            $errorFlag = false;

            if (!empty($this->date_delivery) && !empty($this->date_delivery) && !$validator->validate($date_delivery)) {
                // дата заезда введена не правильно
                $this->addError('date_delivery', 'Некорректный формат');
                $this->addError('date_delivery', '');
                $errorFlag = true;
            }

            if (!$errorFlag) {
                $this->date_delivery = new \DateTime($date_delivery);
                $this->date_delivery = $this->date_delivery->format('Y-m-d H:i:s');

                $time1 = strtotime($this->date_delivery);

                // Бронирование апартаментов не ранее чем за час
                if ((time() + 60 * 5) > $time1) {
                    $this->addError('date_delivery', '');
                    $this->addError('date_delivery', 'Необходимо бронировать не менее чем за 5 минут');
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
    }

    /**
     * Процесс отправки заявки на почтовый адрес
     * @return bool
     */
    public function process()
    {
        $mail = Yii::$app->getMailer();
        $mail->viewPath = '@common/mails';

        $subject = 'Заказ Такси';
        $setTo = 'taxireversi@mail.ru';
        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'departureAddress' => $this->departureAddress,
            'destinationAddress' => $this->destinationAddress,
            'carClass' => $this->getCarClassArray()[$this->carClass],
            'date_delivery' => $this->date_delivery,
        ];

        $send = $mail->compose('taxi', $data)
            ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
            ->setTo($setTo)
            ->setSubject($subject)
            ->send();

        return true;
    }
}