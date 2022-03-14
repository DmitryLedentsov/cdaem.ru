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

    private string $value; // Значение с которым удобство будет связано с апартом

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
     * @throws \yii\db\Exception
     */
    public function getValue(int $apartmentId = 0)
    {
        if (!$apartmentId) {
            return $this->value;
        }

        // TODO разобраться как брать extraColumns стандартно
        $command = \Yii::$app->db->createCommand('select paf.value from {{%partners_apartments_facilities}} paf where paf.apartment_id = :apartment_id and paf.facility_id = :facility_id',
            [
                'apartment_id' => $apartmentId,
                'facility_id' => $this->facility_id
            ]);

        $result = $command->queryAll();
        // dd($result);
        if ($result) {
            $value = $result[0]['value'];
        }

        if ($this->facility_id == '35') {
            // dd($value, $apartmentId, $command->getRawSql());
        }

        if (!is_numeric($value)) {
            $value = unserialize($value);
        }

        return $value;
     }
}
