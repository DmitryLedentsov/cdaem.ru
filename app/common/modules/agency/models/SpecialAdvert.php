<?php

namespace common\modules\agency\models;

use common\modules\agency\traits\ModuleTrait;

/**
 * Специальные предложения
 * @package common\modules\agency\models
 */
class SpecialAdvert extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_special_adverts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advert_id'], 'required'],
            [['advert_id'], 'integer'],
            [['date_expire'], 'safe'],
            [['text'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'special_id' => '№',
            'advert_id' => 'ID объявления',
            'text' => 'Описание',
            'date_start' => 'Дата старта',
            'date_expire' => 'Дата истечения',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentType()
    {
        return $this->hasOne(\common\modules\realty\models\RentType::class, ['rent_type_id' => 'rent_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->getAdvert()->one()->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findActive()
    {
        return static::find()->joinWith([
            'advert' => function ($query) {
                $query->joinWith([
                    'apartment' => function ($query) {
                        $query->visible()->with([
                            'titleImage',
                        ]);
                    },
                ]);
            }
        ])
            ->andWhere('date_expire > :date', [':date' => date("Y-m-d H:i:s")])
            ->andWhere('date_start < :date', [':date' => date("Y-m-d H:i:s")])
            ->all();
    }
}
