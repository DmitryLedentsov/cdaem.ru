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

    private $value; // Значение с которым удобство будет связано с апартом
    // public $value = ''; // Значение с которым удобство будет связано с апартом

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
            'is_extra' => 'Дополнительное удобство',
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

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        // Обрабатываем сохранённый множественный выбор
        if (!is_numeric($this->value)) {
            return unserialize($this->value);
        }

        return $this->value;
    }

    /**
     * @param string $idForSearch
     * @return bool
     */
    public function isValueExist($idForSearch)
    {
        if (!is_array($this->getValue())) {
            return $this->getValue() === (string)$idForSearch;
        }

        foreach ($this->getValue() as $id) {
            if ($id === (string)$idForSearch) {
                return true;
            }
        }

        return false;
    }
}
