<?php

namespace common\modules\partners\models;

use common\modules\partners\traits\ModuleTrait;
use Yii;

/**
 * Проплата открытия контактов на резервации
 * @package common\modules\partners\models
 */
class ReservationsPayment extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_reservations_payment}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'reservation_id' => '№ заявки',
            'user_id' => 'ID пользователя',
            'funds' => 'Средства',
            'date_create' => 'Дата оплаты',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(Reservation::class, ['id' => 'reservation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }
}
