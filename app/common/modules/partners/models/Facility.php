<?php

namespace common\modules\partners\models;

use common\modules\partners\traits\ModuleTrait;

/**
 * Удобства
 * @package common\modules\partners\models
 */
class Facility extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%partners_facilities}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'facility_id' => 'Facility ID',
            'alias' => 'Slug',
            'name' => 'Название удобства',
            'is_active' => 'Активность',
            'date_create' => 'Дата создания',
            'date_update' => 'Дата редактирования',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getApartments()
    {
        return $this->hasMany(Apartment::class, ['apartment_id' => 'apartment_id'])
            ->viaTable('{{%partners_apartments_facilities}}', ['facility_id' => 'facility_id']);
    }
}
