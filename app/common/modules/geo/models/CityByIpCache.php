<?php

namespace common\modules\geo\models;

/**
 * Class CityByIpCache
 * @package common\modules\geo\models
 */
class CityByIpCache extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%city_by_ip_cache}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['city_id' => 'city_id']);
    }
}
