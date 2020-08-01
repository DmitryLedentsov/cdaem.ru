<?php

namespace common\modules\agency\models;

use common\modules\agency\traits\ModuleTrait;
use Yii;

/**
 * Станции метро у апартаментов
 * @package common\modules\agency\models
 */
class ApartmentMetroStations extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_apartment_metro_stations}}';
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
