<?php

namespace common\modules\partners\models;

use common\modules\partners\models\scopes\ReservationFailureQuery;
use common\modules\users\models\User;
use common\modules\partners\traits\ModuleTrait;
use Yii;

/**
 * This is the model class for table "{{%partners_advert_reservations_failure}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $reservation_id
 * @property string $cause_text
 * @property string $date_create
 * @property string $date_update
 * @property string $date_to_process
 * @property integer $processed
 * @property integer $closed
 * @property integer $conflict
 *
 * @property AdvertReservation $reservation
 * @property User $user
 */
class ReservationFailure extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_advert_reservations_failure}}';
    }

    /**
     * @return ReservationFailureQuery
     */
    public static function find()
    {
        return new ReservationFailureQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    /*public function rules()
    {
        return [
            [['user_id', 'reservation_id', 'date_create', 'date_update', 'date_to_process'], 'required'],
            [['user_id', 'reservation_id', 'processed', 'closed'], 'integer'],
            [['date_create', 'date_update', 'date_to_process'], 'safe'],
            ['text', 'string'],
            [['reservation_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnersAdvertReservations::class, 'targetAttribute' => ['reservation_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'user_id' => 'ID пользователя',
            'reservation_id' => '№ Брони',
            'date_create' => 'Дата создания',
            'date_update' => 'Дата редактирования',
            'date_to_process' => 'Дата для возврата',
            'processed' => 'Обработана системой',
            'closed' => 'Автоматический возврат сердств системой',
            'conflict' => 'Конфликт',
            'cause_text' => 'Причина'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(AdvertReservation::class, ['id' => 'reservation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Массив Closed.
     * статус 'closed' значит Включена (0) или выключена (1) автоматическая обработка системой
     * этой заявки.
     * @return array
     */
    public static function getClosedArray()
    {
        return [
            0 => [
                'label' => 'Вкл',
                'style' => 'color: red',
            ],
            1 => [
                'label' => 'Выкл',
                'style' => 'color: green',
            ],
        ];
    }

    /**
     * Массив Conflict.
     * статус 'conflict' значит нету (0) или есть (1) конфликт между оппонентами по заявке "Незаезд"
     * @return array
     */
    public static function getConflictArray()
    {
        return [
            0 => [
                'label' => 'Нету',
                'style' => 'color: green',
            ],
            1 => [
                'label' => 'Есть',
                'style' => 'color: red',
            ],
        ];
    }
}
