<?php

namespace common\modules\agency\models;

use common\modules\agency\models\query\ReservationQuery;
use common\modules\agency\traits\ModuleTrait;
use yii\db\ActiveRecord;
use Yii;

/**
 * Заявки на резервации апартаментов
 * @package common\modules\agency\models
 */
class Reservation extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_apartment_reservations}}';
    }

    /**
     * @return ReservationQuery
     */
    public static function find()
    {
        return new ReservationQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date_create',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reservation_id' => '№',
            'apartment_id' => 'Апартаменты',
            'name' => 'Ф.И.О.',
            'email' => 'EMAIL',
            'clients_count' => 'Количество человек',
            'transfer' => 'Трансфер',
            'date_arrived' => 'Время заезда',
            'date_out' => 'Время съезда',
            'date_create' => 'Дата создания',
            'more_info' => 'Дополнительная информация',
            'whau' => 'Откуда узнали о нас',
            'phone' => 'Телефон для связи',
            'processed' => 'Обработанная',
        ];
    }

    /**
     * Массив данных, откуда о нас узнали
     * @return array
     */
    public static function getWhauArray()
    {
        return [
            1 => 'Поисковая система',
            2 => 'Реклама в интернете',
            3 => 'Газета или журнал',
            4 => 'Порекомендовали знакомые',
            5 => 'Другое',
        ];
    }

    /**
     * Статус
     * - Активный
     * - Не активный
     */
    const PROCESSED = 1;
    const UNPROCESSED = 0;

    /**
     * @return array Массив доступных данных статуса апартаментов
     */
    public static function getProcessedArray()
    {
        $statuses = [
            self::PROCESSED => [
                'label' => 'Обработаннная',
                'color' => 'green',
            ],
            self::UNPROCESSED => [
                'label' => 'Необработанная',
                'color' => 'red',
            ],
        ];

        return $statuses;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }
}
