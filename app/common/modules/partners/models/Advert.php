<?php

namespace common\modules\partners\models;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use common\modules\realty\models\RentType;
use common\modules\partners\traits\ModuleTrait;
use common\modules\partners\models\scopes\AdvertQuery;
use common\modules\realty\models\Apartment as TotalApartment;

/**
 * Объявления
 * @package common\modules\partners\models
 */
class Advert extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_adverts}}';
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
            'position' => 'Позиция',
            'old_position' => 'Прошлая позиция',
            'top' => 'Топовое объявление',
            'top_expire' => 'Дата истечения',
            'newyear' => 'Сдаем на новый год',
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
    public function getAdvertSliders()
    {
        return $this->hasMany(AdvertisementSlider::class, ['advert_id' => 'advert_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * Возвращает подготовленный список типов аренды включая объявления
     * @param array $rentTypesList
     * @param array $adverts
     * @return array
     */
    public static function getPreparedRentTypesAdvertsList(array $rentTypesList, array $adverts)
    {
        $result = [];

        foreach ($rentTypesList as $rentTypeId => $rentTypeName) {
            $result[$rentTypeId] = ['name' => $rentTypeName, 'advert' => null];
            foreach ($adverts as $advert) {
                if ($rentTypeId == $advert->rent_type) {
                    $attributes = $advert->getAttributes();
                    $attributes['price'] = round($attributes['price']);
                    $result[$rentTypeId] = array_merge($result[$rentTypeId], ['advert' => $attributes]);
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Получить полные данные апартамента
     * @param $id
     * @param null $city_name_eng
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getFullData($id, $city_name_eng = null)
    {
        /** @var Advert $advert */
        $advert = Advert::findOne(['advert_id' => $id]);

        if (!$advert) {
            return null;
        }

        // Признак того, что просматривает автор
        $isAuthor = Yii::$app->user->id === $advert->getApartment()->one()->user_id;

        return static::find()
            ->joinWith([
                'apartment' => function ($query) use ($isAuthor) {
                    $usedQuery = $query;

                    if (!$isAuthor) {
                        // Если просматривает не автор, показываем только те объявления которые прошли модерацию
                        $usedQuery = $query->permitted();
                    }

                    $usedQuery
                        ->with([
                            'orderedImages',
                            'metroStations' => function ($query) {
                                $query->with('metro');
                            },
                            //'reservations',
                        ])
                        ->joinWith([
                            'user' => function ($query) {
                                $query->banned(0)->with('profile');
                            },
                            'city' => function ($query) {
                                $query->with('country');
                            },
                        ]);
                },
            ])
            ->with(['rentType'])
            ->where(self::tableName() . '.advert_id = :advert_id', [':advert_id' => $id])
            ->andFilterWhere(['name_eng' => $city_name_eng])
            ->one();
    }

    /**
     * Возвращает другие обьявления пользователя
     * @param $user_id
     * @param $withoutAdvertId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOtherAdverts($user_id, $withoutAdvertId = null)
    {
        //Нужны город, страна, комнаты, цена, тайтлИмедж
        return static::find()
            ->joinWith([
                'apartment' => function ($query) {
                    $query->permitted()
                        ->with([
                            'city',
                            'adverts' => function ($query) {
                                $query->with('rentType');
                            },
                            'titleImage',
                            'metroStation' => function ($query) {
                                $query->with('metro');
                            }
                        ]);
                },
            ])
            ->with(['rentType'])
            ->where(['user_id' => $user_id])
            ->andFilterWhere(['!=', 'advert_id', $withoutAdvertId])
            ->all();
    }

    public static function getAdvertsByUserAndApartmentId($user_id, $apartment_id, $limit = null) {
        $query = static::find()
            ->joinWith([
                'apartment' => function ($query) {
                    $query->permitted()
                        ->with([
                            'city',
                            'titleImage',
                        ]);
                },
            ])
            ->with(['rentType'])
            ->andWhere(['user_id' => $user_id])
            ->andWhere(['partners_adverts.apartment_id' => $apartment_id]);

        if ($limit && is_numeric($limit)) {
            $query->limit($limit);
        }

        return $query->all();
    }

    /**
     * Возвращает все обьявления пользователя
     * @param $limit
     * @param $user_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAdvertsByUser($user_id, $limit = null)
    {
        $query = static::find()
            ->joinWith([
                'apartment' => function ($query) {
                    $query->permitted()
                        ->with([
                            'city',
                            'titleImage',
                        ]);
                },
            ])
            ->with(['rentType'])
            ->andWhere(['user_id' => $user_id]);

        if ($limit && is_numeric($limit)) {
            $query->limit($limit);
        }

        return $query->all();
    }

    public static function getAdvertsOnModerateByUser($user_id, $limit = null)
    {
        $query = static::find()
            ->joinWith([
                'apartment' => function ($query) {
                    $query
                        ->with([
                            'city',
                            'titleImage',
                        ]);
                },
            ])
            ->with(['rentType'])
            ->andWhere(['user_id' => $user_id])
            ->andWhere(['status' => 0]);

        if ($limit && is_numeric($limit)) {
            $query->limit($limit);
        }

        return $query->all();
    }

    /**
     * Поиск объявлений по городу
     * @param $cityId
     * @param bool $top
     * @param bool $rentType
     * @return mixed
     */
    public static function findAdvertsByCity($cityId, $top = false, $rentType = false)
    {
        $top = (bool)$top;
        $pagination = new Pagination([
            'page' => Yii::$app->request->get('page') - 1,
            'defaultPageSize' => Yii::$app->getModule('partners')->pageSizeTop,
        ]);

        $query = static::find()->joinWith([
            'apartment' => function ($query) {
                $query->permitted()
                    ->with([
                        'city',
                        'adverts' => function ($query) {
                            $query->with('rentType');
                        },
                        'titleImage',
                        'metroStation' => function ($query) {
                            $query->with('metro');
                        }
                    ]);
            },
        ])
            ->with('rentType')
            ->andWhere(['city_id' => $cityId])
            ->andFilterWhere(['rent_type' => $rentType])
            ->top($top)
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['position' => SORT_ASC]);

        return $query->all();
    }

    /**
     * Кол-во объявлений
     * @param null $rentType
     * @return int|string
     */
    public static function countAdverts($rentType = null)
    {
        $query = static::find();

        if ($rentType) {
            $query->andWhere('rent_type = :rent_type', [':rent_type' => $rentType]);
        }

        return $query->count();
    }

    /**
     * Возвращает последнюю позицию объявлений
     * @param $city
     * @param $rentTypeId
     * @return bool|int|string
     */
    public static function getLastPosition($city, $rentTypeId)
    {
        $advert = static::find()
            ->select([Advert::tableName() . '.position', Apartment::tableName() . '.apartment_id'])
            ->joinWith(['apartment' => function ($query) use ($city) {
                $query->andWhere([Apartment::tableName() . '.city_id' => $city]);
            }])
            ->andWhere([Advert::tableName() . '.rent_type' => $rentTypeId])
            ->orderBy([Advert::tableName() . '.position' => SORT_DESC])
            ->one();

        if ($advert) {
            return $advert->position;
        }

        return 1;
    }

    /**
     * Список типов аренды
     */
    public function getRentTypesList()
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
     * @return string Текстовое представление валюты
     */
    public function getCurrencyName()
    {
        return ArrayHelper::getValue($this->getCurrencyList(), $this->currency);
    }

    /**
     * Список валюты
     */
    public function getCurrencyList()
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

}
