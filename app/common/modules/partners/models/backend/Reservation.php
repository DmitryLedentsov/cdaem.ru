<?php

namespace common\modules\partners\models\backend;

use yii\db\ActiveRecord;

/**
 * @inheritdoc
 * @package common\modules\partners\models\backend
 */
class Reservation extends \common\modules\partners\models\Reservation
{
    /**
     * Спаренные атрибуты $money_from, $money_to, $currency
     * Всего-лишь для отображения ошибки вместе
     * @var
     */
    public $budget;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        return array_merge($attributeLabels, [
            'budget' => 'Планируемый бюджет'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'ActionBehavior' => [
                'class' => \nepster\users\behaviors\ActionBehavior::class,
                'module' => $this->module->id,
                'actions' => [
                    //ActiveRecord::EVENT_BEFORE_INSERT => 'create-reservation',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update-reservation',
                    ActiveRecord::EVENT_BEFORE_DELETE => 'delete-reservation',
                ],
            ],
        ];

        return array_merge($behaviors, parent::behaviors());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['rent_type', 'city_id', 'address', 'children', 'pets', 'clients_count',
                'more_info', 'money_from', 'money_to', 'currency', 'rooms', 'beds', 'floor', 'metro_walk',
                'date_arrived', 'date_out', 'closed', 'cancel', 'cancel_reason', 'date_actuality'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'exist', 'targetClass' => \common\modules\users\models\User::class, 'targetAttribute' => 'id'],
            ['rent_type', 'in', 'range' => array_keys($this->rentTypesList)],
            ['city_id', 'exist', 'targetClass' => \common\modules\geo\models\City::class, 'targetAttribute' => 'city_id'],
            [['address', 'more_info', 'cancel_reason'], 'string', 'max' => 255],
            ['children', 'in', 'range' => array_keys($this->childrenArray)],
            ['pets', 'in', 'range' => array_keys($this->petsArray)],
            ['clients_count', 'in', 'range' => array_keys($this->clientsCountArray)],
            ['rooms', 'in', 'range' => array_keys($this->roomsList)],
            ['beds', 'in', 'range' => array_keys($this->bedsList)],
            ['floor', 'in', 'range' => array_keys($this->floorArray)],
            ['metro_walk', 'in', 'range' => array_keys($this->metroWalkList)],
            [['date_arrived', 'date_out', 'date_actuality'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_out', 'compare', 'compareAttribute' => 'date_arrived', 'operator' => '>'],
            ['date_out', 'compare', 'compareAttribute' => 'date_arrived', 'operator' => '>='],
            [['closed'], 'boolean'],
            ['cancel', 'in', 'range' => array_keys($this->cancelList)],
            [['money_from', 'money_to'], 'integer', 'min' => 1, 'max' => 1000000, 'message' => 'Значение "Планируемый бюджет" должно быть формата - 100-500 руб'],
            ['currency', 'in', 'range' => array_keys($this->currencyArray)],

            //required
            [['city_id', 'address', 'rooms', 'beds', 'floor', 'metro_walk', 'date_out', 'money_from', 'money_to',
                'currency', 'rent_type', 'user_id', 'children', 'pets', 'clients_count', 'date_arrived', 'date_out',
                'date_actuality', 'more_info', 'cancel', 'closed'], 'required'],


            //required "Причина отмены" когда заявка отменена
            ['cancel_reason', 'required', 'when' => function ($model) {
                return $this->cancel == '2' || $this->cancel == '1';
            }, 'enableClientValidation' => false, 'message' => 'Вы отменили заявку, по-этому заполните это поле'],

            //need to be empty когда заявка не отменена
            ['cancel_reason', 'boolean', 'trueValue' => false, 'falseValue' => false, 'when' => function ($model) {
                return $this->cancel == '0';
            }, 'enableClientValidation' => false, 'message' => 'Заявка не отменена, по-этому очистите это поле'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();

        if (!empty($this->errors['money_from'])) {
            foreach ($this->errors['money_from'] as $error) {
                $this->addError('budget', $error);
            }
        }

        if (!empty($this->errors['money_to'])) {
            foreach ($this->errors['money_to'] as $error) {
                $this->addError('budget', $error);
            }
        }

        if (!empty($this->errors['currency'])) {
            foreach ($this->errors['currency'] as $error) {
                $this->addError('budget', $error);
            }
        }
    }
}
