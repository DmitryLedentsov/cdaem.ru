<?php

namespace common\modules\geo\models;

/**
 * Class Disctricts
 * @package common\modules\geo\models
 */
class Districts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%districts}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['city_id' => 'city_id']);
    }

    /**
     * @param string $alias
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function findByAlias(string $alias)
    {
        return static::find()
            ->where(['name_eng' => $alias])
            ->one();
    }
}
