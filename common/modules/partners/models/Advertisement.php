<?php

namespace common\modules\partners\models;

use common\modules\partners\traits\ModuleTrait;
use Yii;

/**
 * Реклама
 * @package common\modules\partners\models
 */
class Advertisement extends \yii\db\ActiveRecord
{
    use ModuleTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_advertisement}}';
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::className(), ['advert_id' => 'advert_id']);
    }

    /**
     * Актуальные объявления
     * @param null $city_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRelevantAds($city_id = null)
    {
        return static::find()
            ->joinWith([
                'advert' => function ($query) {
                    $query->joinWith([
                        'apartment' => function ($query) {
                            $query
                                ->permitted()
                                ->joinWith([
                                    'user' => function ($query) {
                                        $query->banned(0);
                                    },
                                ]);
                        },
                    ]);
                },
            ])
            ->andFilterWhere([
                Apartment::tableName() . '.city_id' => $city_id,
            ])
            ->all();
    }
}
