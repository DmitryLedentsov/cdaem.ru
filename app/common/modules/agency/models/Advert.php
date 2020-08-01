<?php

namespace common\modules\agency\models;

use common\modules\agency\models\query\AdvertQuery;
use common\modules\agency\traits\ModuleTrait;
use common\modules\realty\models\Apartment as TotalApartment;
use common\modules\realty\models\RentType;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Объявления апартаментов
 * @package common\modules\agency\models
 */
class Advert extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_adverts}}';
    }

    /**
     * @return AdvertQuery
     */
    public static function find()
    {
        return new AdvertQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'advert_id' => '№',
            'apartment_id' => 'Апартаменты',
            'rent_type' => 'Тип аренды',
            'price' => 'Стоимость аренды',
            'currency' => 'Валюта',
            'main_page' => 'На главной',
            'meta_title' => 'Заголовок (Title)',
            'meta_description' => 'Описание (Description)',
            'meta_keywords' => 'Ключевые слова (Keywords)',
            'text' => 'Текст',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentType()
    {
        return $this->hasOne(RentType::class, ['rent_type_id' => 'rent_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvertisement()
    {
        return $this->hasOne(Advertisement::class, ['advert_id' => 'advert_id'])
            ->andWhere('date_expire >= :date', [':date' => date('Y-m-d H:i:s')]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(SpecialAdvert::class, ['advert_id' => 'advert_id'])
            ->andWhere('date_expire >= :date', [':date' => date('Y-m-d H:i:s')]);
    }

    /**
     * Возвращает все данные объявлений
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getFullData($id)
    {
        return static::find()
            ->joinWith([
                'apartment' => function ($query) {
                    $query->visible()
                        ->with([
                            'user' => function ($query) {
                                $query->with('profile');
                            },
                            'city' => function ($query) {
                                $query->with('country');
                            },
                            'orderedImages',
                            'metroStations' => function ($query) {
                                $query->with('metro');
                            },
                            'mainDistrict',
                            'semiDistrict',
                            'reservations',
                        ]);
                },
            ])
            ->with(['rentType'])
            ->where(self::tableName() . '.advert_id = :advert_id', [':advert_id' => $id])
            ->one();
    }

    /**
     * Возвращает ActiveQuery с нужными для предпросмотра реляциями
     * @return \yii\db\ActiveQuery
     */
    public static function previewFind()
    {
        return static::find()->joinWith([
            'apartment' => function ($query) {
                $query->visible()
                    ->with([
                        'adverts' => function ($query) {
                            $query->with('rentType');
                        },
                        'titleImage',
                        'mainDistrict',
                    ])
                    ->joinWith([
                        'metroStations' => function ($query) {
                            $query->with('metro');
                        }
                    ]);
            },
        ])
            ->with('rentType');
    }

    /**
     * @return array Список типов аренды
     */
    public static function getRentTypesList()
    {
        return RentType::rentTypeslist();
    }

    /**
     * @return string Текстовое представление Типа аренды этой записи
     */
    public function getRentTypeName()
    {
        return ArrayHelper::getValue($this->rentTypesList, $this->rent_type);
    }

    /**
     * @return array Список валюты
     */
    public static function getCurrencyList()
    {
        return TotalApartment::getCurrencyArray();
    }

    /**
     * Текстовое представление цены
     * @return string
     */
    public function getPriceText()
    {
        return Yii::$app->formatter->asCurrency($this->price, ArrayHelper::getValue($this->currencyList, $this->currency));
    }

    /**
     * Список доступных значений поля main_page
     */
    const MAIN_PAGE = 1; // Отображается на главной странице
    const NOT_MAIN_PAGE = 0; // Не отображается

    /**
     * @return array Массив доступных значений поля main_page в текстовом представлении
     */
    public function getMainPageArray()
    {
        return [
            self::MAIN_PAGE => [
                'label' => 'Да',
                'style' => 'color: green',
            ],
            self::NOT_MAIN_PAGE => [
                'label' => 'Нет',
                'style' => 'color: red',
            ],
        ];
    }
}