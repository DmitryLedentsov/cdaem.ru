<?php

namespace common\modules\partners\models;

use common\modules\partners\models\scopes\ReservationQuery;
use common\modules\realty\models\Apartment as TotalApartment;
use common\modules\realty\models\RentType;
use yii\helpers\ArrayHelper;
use common\modules\partners\traits\ModuleTrait;
use Yii;

/**
 * Резервации
 * @package common\modules\partners\models
 */
class Reservation extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_reservations}}';
    }

    /**
     * @return ReservationQuery
     */
    public static function find()
    {
        return new ReservationQuery(get_called_class());
    }

    /**
     * Возвращает количество непросмотренных заявок owner'а или renter'а
     * @param string $byWhom
     * @param null $city_id
     * @return int|string
     */
    public static function nonViewedCount($byWhom = 'renter', $city_id = null)
    {
        $lastViewedDate = UserSeen::getLastDate(self::tableName(), $byWhom . $city_id);
        $query = self::find()->where(['>', 'date_update', $lastViewedDate]);

        if ($byWhom == 'owner') {
            $query->andFilterWhere(['city_id' => $city_id]);
            $query->andWhere(['!=', 'user_id', Yii::$app->user->id])
                ->notClosedAndCancel()->actual();
        } else {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }

        return $query->count();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'user_id' => 'Пользователь',
            'rent_type' => 'Тип аренды',
            'city_id' => 'Город',
            'address' => 'Адрес',
            'children' => 'Дети',
            'pets' => 'Животные',
            'clients_count' => 'Кол-во человек',
            'more_info' => 'Пожелания',
            'rooms' => 'Кол-во комнат',
            'money_from' => 'Планируемый бюджет от',
            'money_to' => 'Планируемый бюджет до',
            'currency' => 'Валюта бюджета',
            'beds' => 'Спальных мест',
            'floor' => 'Этаж',
            'metro_walk' => 'Расстояние от метро',
            'date_arrived' => 'Дата заезда',
            'date_out' => 'Дата выезда',
            'cancel' => 'Отменена',
            'cancel_reason' => 'Причина отмены',
            'closed' => 'Закрыта',
            'date_create' => 'Дата создания',
            'date_actuality' => 'Дата истечения актуальности',
        ];
    }

    /**
     * Значения полей cancel
     */
    const CLIENT = 1;
    const OWNER = 2;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(\common\modules\geo\models\City::class, ['city_id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(ReservationsPayment::class, ['reservation_id' => 'id']);
    }

    /**
     * Возвращает открыты ли контакты резервации
     * @param null $userId
     * @return bool
     */
    public function getIsContactsOpen($userId = null)
    {
        return ReservationsPayment::find()
            ->where([
                'reservation_id' => $this->id,
                'user_id' => $userId ? $userId : Yii::$app->user->id,
            ])->exists();
    }

    /**
     * Возвращает количество активных на данный момент резерваций
     * @return integer
     */
    public static function globalActiveCount($city_id = null)
    {
        $query = static::find()
            ->joinWith([
                'user' => function ($query) {
                    $query->banned(0);
                },
            ])
            ->andWhere(['!=', self::tableName() . '.user_id', Yii::$app->user->id])
            ->notClosedAndCancel()
            ->actual();

        $query->andFilterWhere([self::tableName() . '.city_id' => $city_id]);

        return $query->count();
    }

    /**
     * Возвращает количество резерваций, с открытми контактами для этого пользователя
     * @return integer
     */
    public static function globalOpenCount()
    {
        return static::find()
            ->joinWith([
                'user' => function ($query) {
                    $query->banned(0);
                },
                'payments'
            ], true, 'RIGHT JOIN')
            ->andWhere([ReservationsPayment::tableName() . '.user_id' => Yii::$app->user->id])
            ->count();
    }

    /**
     * Массив доступных значений Животные
     * @return array
     */
    public function getPetsArray()
    {
        return [
            0 => 'Без животных',
            1 => 'С животными',
        ];
    }

    /**
     * Текстовое представление значения Животные
     * @return string
     */
    public function getPetsText()
    {
        return ArrayHelper::getValue($this->petsArray, $this->pets);
    }

    /**
     * Массив достпуных значений Дети
     * @return array
     */
    public function getChildrenArray()
    {
        return [
            0 => 'Без детей',
            1 => 'С детьми',
        ];
    }

    /**
     * Текстовое представление значения Дети
     * @return string
     */
    public function getChildrenText()
    {
        return ArrayHelper::getValue($this->childrenArray, $this->children);
    }

    /**
     * Массив доступных значений Этаж
     * @return array
     */
    public function getFloorArray()
    {
        return [
            0 => 'Не важно',
            1 => 'Только первый',
            2 => 'Не первый',
            3 => 'Не последний',
        ];
    }

    /**
     * Текстовое представление значения Этаж
     * @return string
     */
    public function getFloorText()
    {
        return ArrayHelper::getValue($this->floorArray, $this->floor);
    }

    /**
     * Массив доступных значений Кол-во человек
     * @return array
     */
    public function getClientsCountArray()
    {
        return [
            1 => 'Один',
            2 => 'Два',
            3 => 'Три',
            4 => 'Четыре',
            5 => 'Пять',
            6 => 'Шесть и более',
        ];
    }

    /**
     * Текстовое представление значения Кол-во человек
     * @return string
     */
    public function getClientsCountText()
    {
        return ArrayHelper::getValue($this->clientsCountArray, $this->clients_count);
    }

    /**
     * Массив доступных начений Продолжительности актуальности заявки
     * @return array
     */
    public function getActualityList()
    {
        return [
            1 => '15 минут',
            2 => '30 минут',
            3 => '60 минут',
            4 => '24 часа',
            5 => 'До даты заезда',
        ];
    }

    /**
     * Возвращает отформатированную строку планируемого бюджета
     * @return string
     */
    public function getBudgetString()
    {
        $from = $this->money_from;
        $to = $this->money_to;
        $currency = $this->currency;

        $result = [];

        if ($currency) {
            $currency = ArrayHelper::getValue($this->getCurrencyArray(), $currency);
        }

        if ($from) {
            $result[] = Yii::$app->formatter->asCurrency($from, $currency);
        }

        if ($to) {
            $result[] = Yii::$app->formatter->asCurrency($to, $currency);
        }

        if ($from && $to && $from == $to) {
            unset($result[0]);
        }

        return implode(' - ', $result);
    }

    /**
     * @inheritdoc
     */
    public function getMetroWalkList()
    {
        return TotalApartment::getMetroWalkArray();
    }

    /**
     * Тектовое представления значения "дальность от метро"
     * @return mixed
     */
    public function getMetroWalkText()
    {
        return ArrayHelper::getValue($this->metroWalkList, $this->metro_walk);
    }

    /**
     * Массив статуса отмен
     * @return array
     */
    public function getCancelList()
    {
        return [
            0 => 'Активная',
            1 => 'Отменено создателем',
            2 => 'Отменено арендодателем',
        ];
    }

    /**
     * Текстовое представление текущего значения статуса отмены
     * @return string
     */
    public function getCancelText()
    {
        return ArrayHelper::getValue($this->cancelList, $this->cancel);
    }

    /**
     * Массив доступных значений Валюта
     * @return array
     */
    public function getCurrencyArray()
    {
        return TotalApartment::getCurrencyArray();
    }

    /**
     * Массив доступных значений Рент Типа
     * @return array
     */
    public function getRentTypesList()
    {
        return RentType::rentTypeslist();
    }

    /**
     * Текстовое представление Типа аренды этой записи
     * @return string
     */
    public function getRentTypeText()
    {
        return ArrayHelper::getValue($this->rentTypesList, $this->rent_type);
    }

    /**
     * Массив доступных значений Комнат
     * @return array
     */
    public function getRoomsList()
    {
        return TotalApartment::getRoomsArray();
    }

    /**
     * Текстовое представления Комнат
     * @return string
     */
    public function getRoomsText()
    {
        return ArrayHelper::getValue($this->roomsList, $this->rooms);
    }

    /**
     * Массив доступных значений Спальные места
     * @return array
     */
    public function getBedsList()
    {
        return TotalApartment::getBedsArray();
    }

    /**
     * Текстовое представление значения Спальных мест
     * @return string
     */
    public function getBedsText()
    {
        return ArrayHelper::getValue($this->bedsList, $this->beds);
    }
}