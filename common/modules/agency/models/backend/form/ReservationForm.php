<?php

namespace common\modules\agency\models\backend\form;

use common\modules\agency\models\Reservation;
use Yii;

/**
 * Reservation Form
 * @package common\modules\agency\models\backend\form
 */
class ReservationForm extends \yii\base\Model
{
    public $reservation_id;
    public $apartment_id;
    public $name;
    public $email;
    public $clients_count;
    public $transfer;
    public $date_arrived;
    public $date_out;
    public $more_info;
    public $whau;
    public $phone;
    public $processed;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['apartment_id', 'name', 'email', 'clients_count', 'transfer', 'date_arrived', 'date_out', 'more_info', 'whau', 'phone'],
            'update' => ['apartment_id', 'name', 'email', 'clients_count', 'transfer', 'date_arrived', 'date_out', 'more_info', 'whau', 'phone', 'processed'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Reservation())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_id', 'clients_count', 'transfer', 'whau', 'processed'], 'integer'],
            [['apartment_id', 'clients_count', 'transfer', 'whau', 'processed'], 'filter', 'filter' => 'intval'],
            [['date_arrived', 'date_out'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_out', 'compare', 'compareAttribute' => 'date_arrived', 'operator' => '>='],
            [['transfer', 'processed'], 'boolean'],
            ['apartment_id', 'exist', 'targetClass' => '\common\modules\agency\models\Apartment', 'targetAttribute' => 'apartment_id'],

            [['name', 'email', 'more_info'], 'string', 'max' => 255],
            ['email', 'email'],
            ['phone', 'number'],
            // required on
            [['apartment_id', 'name', 'email', 'clients_count', 'transfer', 'date_arrived', 'date_out', 'whau', 'phone'], 'required', 'on' => 'create'],
            [['apartment_id', 'name', 'email', 'clients_count', 'transfer', 'date_arrived', 'date_out', 'whau', 'phone'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * Создать
     *
     * @return bool
     */
    public function create()
    {
        $model = new Reservation();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');
        $model->processed = 1;

        if (!$model->save(false)) {
            return false;
        }

        $this->reservation_id = $model->reservation_id;
        return true;
    }

    /**
     * Редактировать
     *
     * @param Reservation $model
     * @return bool
     */
    public function update(Reservation $model)
    {
        $model->setAttributes($this->getAttributes(), false);
        return $model->save(false);
    }
}
