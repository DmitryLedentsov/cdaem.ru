<?php

namespace common\modules\partners\models;

use common\modules\partners\models\scopes\AdvertisementSliderQuery;
use common\modules\partners\traits\ModuleTrait;
use Yii;

/**
 * Реклама в бегущей строке
 * @package common\modules\partners\models
 */
class AdvertisementSlider extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_advertisement_slider}}';
    }

    /**
     * @return AdvertisementSliderQuery
     */
    public static function find()
    {
        return new AdvertisementSliderQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип',
            'label' => 'Метка',
            'advert_id' => 'Объявление',
            'user_id' => 'Пользователь',
            'more_info' => 'Дополнительная информация',
            'visible' => 'Показывать',
            'payment' => 'Оплачено',
        ];
    }

    /**
     * Получить список активных объявлений
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAdvertisementList($limit, $city_id = null)
    {
        $query = static::find()
            ->joinWith([
                'advert' => function ($query) {
                    $query->joinWith([
                        'apartment' => function ($query) {
                            $query
                                ->permitted()
                                ->orWhere(Apartment::tableName() . '.visible IS NULL')
                                ->with([
                                    'city' => function ($query) {
                                        $query->with('country');
                                    },
                                    'titleImage',
                                    /*'metroStations' => function ($query) {
                                        $query->with('metro');
                                    },*/
                                ]);
                        }
                    ]);
                },
                'user' => function ($query) {
                    $query->banned(0);
                },
            ])
            ->visible()
            ->payment()
            ->orderBy(['date_payment' => SORT_DESC])
            ->limit($limit);

        if ($city_id) {
            $query->andWhere(['OR', Apartment::tableName() . '.city_id = :city_id', self::tableName() . '.advert_id IS NULL'])
                ->addParams([':city_id' => $city_id]);
        }

        return $query->all();
    }

    /**
     * Типы объявлений
     * - Cдам
     * - Есть клиент
     * - Сниму
     */
    const TYPE_RENT = 1;
    const TYPE_CLIENT = 2;
    const TYPE_RESERV = 3;

    /**
     * Типы объявлений
     * @return array
     */
    public static function getTypeArray()
    {
        return [
            self::TYPE_RENT => 'Cдам',
            self::TYPE_CLIENT => 'Есть клиент',
            self::TYPE_RESERV => 'Сниму',
        ];
    }

    /**
     * Метки объявлений
     * @return array
     */
    public static function getLabelArray()
    {
        return [
            1 => 'Сейчас свободно',
            2 => 'Есть клиент',
            3 => 'Скидка',
            4 => 'Хочу снять',
            5 => 'Указать сумму',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::className(), ['id' => 'user_id']);
    }
}
