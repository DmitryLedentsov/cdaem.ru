<?php

namespace common\modules\partners\models;

use common\modules\partners\traits\ModuleTrait;
use Yii;

/**
 * Календарь бронирования
 * @package common\modules\partners\models
 */
class Calendar extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * Статус
     * - Зарезервировано
     * - свободно
     */
    const RESERVED = 1;
    const FREE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_calendar}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reserved' => 'Зарезервировано',
            'date_from' => 'Дата заезда',
            'date_to' => 'Дата выезда',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * Получить все записи календаря для апартаментов
     * @param $apartment_id
     * @param null $scope
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findByApartmentId($apartment_id, $scope = null)
    {
        $query = self::find();
        $query->where('apartment_id = :apartment_id', [':apartment_id' => $apartment_id]);
        if ($scope) {
            $query->andWhere('reservation = :reservation', [':reservation' => $scope]);
        }
        return $query->all();
    }

    /**
     * Поиск записей в диапазоне дат
     * @param $start
     * @param $end
     * @param null $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findRecordsByDateInterval($start, $end, $userId = null)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        return static::find()
            ->joinWith([
                'apartment' => function ($query) use ($userId) {
                    $query->andWhere(['user_id' => $userId]);
                }
            ])
            ->andWhere(':date_from <= DATE(date_from) AND :date_from <= DATE(date_to)', [':date_from' => $start])
            ->orderBy(['reserved' => SORT_ASC, 'date_from' => SORT_ASC])
            ->all();
    }

    /**
     * Поиск записи за определенную дату
     * @param $date
     * @param null $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findRecordsByDate($date, $userId = null)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        return static::find()
            ->joinWith([
                'apartment' => function ($query) use ($userId) {
                    $query->andWhere(['user_id' => $userId]);
                }
            ])
            ->andWhere('DATE(date_from) <= :date AND DATE(date_to) >= :date', [':date' => $date])
            ->orderBy(['date_from' => SORT_ASC, 'reserved' => SORT_ASC])
            ->all();
    }

    /**
     *
     * @param $apartmentId
     * @param $date
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findApartmentRecordByDate($apartmentId, $date)
    {
        return static::find()
            ->andWhere('apartment_id = :apartment_id', [':apartment_id' => $apartmentId])
            ->andWhere('DATE(date_from) <= :date AND DATE(DATE_SUB(date_to, INTERVAL 1 SECOND)) >= :date', [':date' => $date])
            ->orderBy(['date_from' => SORT_ASC, 'reserved' => SORT_ASC])
            ->one();
    }

    /**
     * Проверяет есть ли резервация в указанную дату
     * Проверят не пересекаются ли два временных промежутка 1. Вводные данные; 2. Из записи таблицы calendar
     * @param $apartmentId Может принимать как Integer так и Array
     * @param $date_start
     * @param $date_to
     * @return int|string
     */
    public static function hasReserved($apartmentId, $date_start, $date_to)
    {
        return self::find()
            ->where(['apartment_id' => $apartmentId, 'reserved' => self::RESERVED])
            ->andWhere(['OR',
                ':date_from BETWEEN date_from AND date_to',
                ':date_to BETWEEN date_from AND date_to',
                'date_from BETWEEN :date_from AND :date_to',
                'date_to BETWEEN :date_from AND :date_to'
            ])
            ->addParams([':date_from' => $date_start, ':date_to' => $date_to])
            ->limit(1)
            ->count();
    }

    /**
     * Текущий статус аренды
     * @return array
     */
    public function getStatusArray()
    {
        return [
            -1 => 'Не учитывать',
            0 => 'Сейчас свободно',
            1 => 'Занято'
        ];
    }
}