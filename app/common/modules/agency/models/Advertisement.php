<?php

namespace common\modules\agency\models;

use common\modules\agency\traits\ModuleTrait;

/**
 * Реклама
 * @package common\modules\agency\models
 */
class Advertisement extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_advertisement}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'advertisement_id' => '№',
            'advert_id' => 'ID объявления',
            'text' => 'Текст рекламы',
            'date_start' => 'Дата старта',
            'date_expire' => 'Дата истечения',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }

    /**
     * Актуальные объявления
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRelevantAds()
    {
        return static::find()
            ->with([
                'advert'
            ])
            ->where('date_expire > :date', [':date' => date("Y-m-d H:i:s")])
            ->andWhere('date_start < :date', [':date' => date("Y-m-d H:i:s")])
            ->all();
    }
}
