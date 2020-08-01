<?php

namespace common\modules\agency\models\form;

use common\modules\agency\models\Reservation;
use Yii;

/**
 * Reservation Form
 * @package common\modules\agency\models\form
 */
class ReservationForm extends \yii\base\Model
{
    public $reservation_id;
    public $name;
    public $email;
    public $phone;
    public $transfer;
    public $apartment_id;
    public $clients_count;
    public $more_info;
    public $whau;
    public $arrived_date;
    public $arrived_time;
    public $out_date;
    public $out_time;
    public $verifyCode;
    public $date_arrived;
    public $date_out;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'Reservation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Обязательные атрибуты
            [['name', 'phone', 'email', 'arrived_date', 'arrived_time', 'out_date', 'out_time', 'clients_count'], 'required'],

            // Email
            ['email', 'email'],

            // Телефон
            ['phone', 'number'],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],

            // Кол-во клиентов
            ['clients_count', 'integer', 'min' => 1, 'max' => 99],

            // Откуда о нас узнали
            ['whau', 'in', 'range' => array_keys($this->getWhauArray())],

            // Дополнительная информация
            ['more_info', 'string', 'max' => 1000],

            // Имя пользователя
            ['name', 'string'],

            // Трансфер
            ['transfer', 'boolean'],

            // Защитный код
            ['verifyCode', 'required', 'message' => 'Подтвердите, что Вы не робот'],
            ['verifyCode', \common\modules\site\widgets\Captcha::getClassValidator()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Reservation)->attributeLabels(), [
            'verifyCode' => 'Защитный код',

            'arrived_date' => 'Дата заезда',
            'arrived_time' => 'Время',

            'out_date' => 'Дата выезда',
            'out_time' => 'Время',
        ]);
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


        $validator = new \yii\validators\DateValidator();
        $validator->format = 'php:d.m.Y H:i';

        $date_arrived = trim($this->arrived_date . ' ' . $this->arrived_time);
        $date_out = trim($this->out_date . ' ' . $this->out_time);

        $errorFlag = false;


        if (!empty($this->arrived_date) && !empty($this->arrived_time) && !$validator->validate($date_arrived)) {
            // дата заезда введена не правильно
            $this->addError('arrived_date', 'Некорректный формат');
            $this->addError('arrived_time', '');
            $errorFlag = true;
        }

        if (!empty($this->out_date) && !empty($this->out_time) && !$validator->validate($date_out)) {
            // дата выезда введена не правильно
            $this->addError('out_date', 'Некорректный формат');
            $this->addError('out_time', '');
            $errorFlag = true;
        }


        if (!$errorFlag) {

            $this->date_arrived = new \DateTime($date_arrived);
            $this->date_arrived = $this->date_arrived->format('Y-m-d H:i:s');

            $this->date_out = new \DateTime($date_out);
            $this->date_out = $this->date_out->format('Y-m-d H:i:s');


            $time1 = strtotime($this->date_arrived);
            $time2 = strtotime($this->date_out);

            // Дата съезда должна быть больше даты въезда
            if ($time1 > $time2) {
                $this->addError('out_date', 'Увеличьте дату или время сьезда');
                $this->addError('out_time', '');
            }

            // Дата съезда должна быть больше даты въезда минимум на 2 часа
            if ((($time2 - $time1) / 60 / 60) < 2) {
                $this->addError('out_time', 'Минимальное время аренды 2 часа');
            }

        }

        return true;
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Reservation;
        $model->setAttributes($this->getAttributes(), false);
        if (!$model->save(false)) {
            return false;
        }
        $model->date_create = date('Y-m-d H:i:s');
        $this->reservation_id = $model->reservation_id;
        return true;
    }

    /**
     * @return array
     */
    public static function getWhauArray()
    {
        return Reservation::getWhauArray();
    }

}