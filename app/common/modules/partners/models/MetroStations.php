<?php

namespace common\modules\partners\models;

use Yii;

/**
 * Станции метро
 * @package common\modules\partners\models
 */
class MetroStations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_apartment_metro_stations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_id', 'metro_id'], 'required'],
            [['apartment_id', 'metro_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'apartment_id' => 'ID города',
            'metro_id' => 'ID города',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetro()
    {
        return $this->hasOne(\common\modules\geo\models\Metro::class, ['metro_id' => 'metro_id']);
    }
}
