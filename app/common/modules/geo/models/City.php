<?php

namespace common\modules\geo\models;

use yii\db\Connection;
use Yii;

/**
 * Class City
 * @package common\modules\geo\models
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%city}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['country_id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['region_id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetro()
    {
        return $this->hasMany(Metro::className(), ['city_id' => 'city_id']);
    }

    /**
     * Поиск города по названию
     * @param $name
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByName($name)
    {
        return self::findOne(['name' => $name]);
    }

    /**
     * Поиск города по сокращенному названию транслитерацией
     * @param $name_eng
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByNameEng($name_eng)
    {
        if (!$name_eng) return null;
        return self::findOne(['name_eng' => $name_eng]);
    }

    /**
     * Поиск города по id
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findById($id)
    {
        return self::find()
            ->where('city_id = :city_id', [':city_id' => $id])
            ->one();
    }

    /**
     * @return bool
     */
    public function hasMetro()
    {
        if ($this->city_id == 4400) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает список всех городов в формате для выпадающего списка
     * @return array
     * @throws \Exception
     */
    public static function dropDownList()
    {
        $duration = 31556926;  // 1 год
        $dependency = null;

        return Yii::$app->db->cache(function (Connection $db) {

            $rows = (new \yii\db\Query())
                ->select(self::tableName() . '.city_id, ' . self::tableName() . '.name, ' . Country::tableName() . '.name AS country')
                ->from(self::tableName())
                ->leftJoin(Country::tableName(), Country::tableName() . '.country_id = ' . self::tableName() . '.country_id')
                ->orderBy([self::tableName() . '.name' => SORT_ASC])
                ->all();

            $result = [];

            foreach ($rows as &$row) {
                $result[$row['city_id']] = $row['name'] . ' ' . '(' . $row['country'] . ')';
                unset($row);
            }

            unset($rows);

            return $result;

        }, $duration, $dependency);
    }
}
