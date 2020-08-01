<?php

namespace common\modules\partners\models;

use common\modules\partners\models\scopes\ReservationDealQuery;
use Yii;

/**
 * История сделок бронирования
 * @package common\modules\partners\models
 */
class ReservationDeal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_advert_reservations_deal}}';
    }

    /**
     * @inheritdoc
     * @return ReservationDealQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReservationDealQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'reservation_id' => 'ID резервации',
            'payment_owner' => 'Заявка оплачена владельцем',
            'payment_client' => 'Заявка оплачена клиентом',
            'date_payment_owner' => 'Дата оплаты владельца',
            'date_payment_client' => 'Дата оплаты клиента',
            'funds_owner' => 'Сердства владельца',
            'funds_client' => 'Средства клиента'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(AdvertReservation::class, ['reservation_id' => 'reservation_id']);
    }

    /**
     * Типы
     * - Владелец оплатил
     * - Клиент оплатил
     */
    const CONFIRM_OWNER = 'RESERVATION_CONFIRM_OWNER';
    const CONFIRM_CLIENT = 'RESERVATION_CONFIRM_CLIENT';
    const RETURN_MONEY = 'RESERVATION_RETURN_MONEY';
    const RETURN_MONEY_FAILURE = 'RESERVATION_RETURN_MONEY_OF_FAILURE';

    /**
     * Массив доступных данных
     * @return array
     */
    public static function getTypesArray()
    {
        return [
            self::CONFIRM_OWNER => [
                'label' => 'Подтвердил заявку',
                'style' => '',
            ],
            self::CONFIRM_CLIENT => [
                'label' => 'Подтвердил заявку',
                'style' => '',
            ],
            self::RETURN_MONEY => [
                'label' => 'Возврат по неподтверждению',
                'style' => '',
            ],
            self::RETURN_MONEY_FAILURE => [
                'label' => 'Возврат по "Незаезду"',
                'style' => '',
            ]
        ];
    }
}
