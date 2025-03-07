<?php

namespace common\modules\partners\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\modules\geo\models\City;
use common\modules\geo\models\Country;
use common\modules\partners\traits\ModuleTrait;
use common\modules\partners\models\scopes\ApartmentQuery;
use common\modules\realty\models\Apartment as TotalApartment;

/**
 * Апартаменты
 * @package common\modules\partners\models
 */
class Apartment extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_apartments}}';
    }

    /**
     * @inheritdoc
     * @return ApartmentQuery
     */
    public static function find()
    {
        return new ApartmentQuery(get_called_class());
    }

    /**
     * Удаляем картинки с БД и с дискаактивирована
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Image::deleteAllWithFiles(['apartment_id' => $this->apartment_id]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'apartment_id' => '№',
            'user_id' => 'Пользователь',
            'city_id' => 'Город',
            'city_name' => 'Город',
            'region_name' => 'Город',
            'closest_city_id' => 'Ближайший город',
            'address' => 'Адрес',
            'apartment' => 'Номер квартиры',
            'floor' => 'Этаж',
            'total_rooms' => 'Количество комнат',
            'total_area' => 'Общая площадь',
            'visible' => 'Отображение',
            'status' => 'Статус',
            'remont' => 'Ремонт',
            'beds' => 'Кол-во кроватей',
            'sleeping_place' => 'Кол-во спальных мест',
            'metro_walk' => 'Расстояние от метро',
            'description' => 'Описание',
            'suspicious' => 'Подозрения',
            'date_create' => 'Дата создания',
            'date_update' => 'Дата редактирования',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetroStations()
    {
        return $this->hasMany(MetroStations::class, ['apartment_id' => 'apartment_id']);
    }

    public function getFirstMetroStations()
    {
        $metroList = $this->getMetroStations()->all();

        if ($metroList) {
            return array_values($metroList)[0];
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdverts()
    {
        return $this->hasMany(Advert::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['city_id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(\common\modules\reviews\models\Review::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return bool|int|string|null
     */
    public function getReviewsCount()
    {
        return \common\modules\reviews\models\Review::find()
            ->where(['apartment_id' => $this->apartment_id])
            ->moderation()
            ->count();
    }

    /**
     * Поиск апартаментов по идентификатору
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findById($id)
    {
        return static::find()
            ->where('{{%partners_apartments}}.apartment_id = :apartment_id', [':apartment_id' => $id])
            ->joinWith(['metroStations' => function ($query) {
                $query->joinWith('metro');
            }])
            ->one();
    }

    /**
     * Поиск апартаментов по идентификатору
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByIdThisUser($id)
    {
        return static::find()
            ->where([self::tableName() . '.apartment_id' => $id, 'user_id' => Yii::$app->user->id])
            ->joinWith(['metroStations' => function ($query) {
                $query->joinWith('metro');
            }])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleImage()
    {
        return $this->hasOne(Image::class, ['apartment_id' => 'apartment_id'])->andWhere(['default_img' => 1]);
    }

    /**
     * Возвращает все апартаменты пользователя
     * @param $limit
     * @param $user_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findApartmentsByUser($user_id, $limit = null)
    {
        $query = static::find()
            ->with([
                'city',
                'titleImage',
                //'calenderActualRecord',
            ])
            ->permitted()
            ->andWhere(['user_id' => $user_id]);

        if ($limit && is_numeric($limit)) {
            $query->limit($limit);
        }

        return $query->all();
    }

    /**
     * Возвращает апартамент по $apartmentId и $userId
     * @param $apartmentId
     * @param $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findApartmentByUser($apartmentId, $userId)
    {
        return static::find()
            ->andWhere(['apartment_id' => $apartmentId])
            ->andWhere(['user_id' => $userId])
            ->one();
    }

    /**
     * Возвращает все апартаменты пользователя, которые сейчас свободны
     * @param null $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findApartmentsByAvailable($userId = null)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        $query = static::find()
            ->with([
                'city',
                'titleImage',
            ])
            ->permitted()
            ->available()
            ->andWhere(['user_id' => $userId]);

        return $query->all();
    }

    /**
     * Отсортированный массив городов и стран, где есть апартаменты
     * @return array
     */
    public static function alphCities()
    {
        $arCountries = [];

        $arCountry = self::find()->select([self::tableName().'.city_id'])
            ->leftJoin('city', self::tableName().'.`city_id` = `city`.`city_id`')
            ->where('city.country_id = 3159') // Показываем только Россию
            ->distinct()
            ->asArray()
            ->all();
        // Вынял все города в которых есть квартиры

        foreach ($arCountry as $country) {
            array_push($arCountries, $country['city_id']);
        }

        // Сбил ключи получился просто массив city_id в которых есть квартиры
        $result = City::find()
            ->select(['city_id', 'city.name', 'name_eng', 'country.name as country_name'])
            ->leftJoin(Country::tableName(), 'country.country_id = city.country_id')
            ->where(['city_id' => $arCountries])
            ->orderBy('city.country_id, city.name')
            ->asArray()
            ->all();

        // Вынул все города LEFT JOIN с коунтрис
        // Так что получились массив записей name(город), name_eng, country_name
        // и с помощью MySQL отсортировал по КоунтриАйди(города) и name(города)
        // Получается Записи Идут сночала все города Беларуси при чем города отсортированы по имени

        $arSorter = [];
        $alphCitiesList = [];
        foreach ($result as $city) {
            // Каждого города берется первая буква
            $firstLetter = mb_substr($city['name'], 0, 1, 'utf-8');
            // И засовывает в массив где ключ [Название страны][ПерваяБукваГорода]
            $alphCitiesList[$city['country_name']][$firstLetter][] = $city;

            // Записывает количество городов Страны
            if (!isset($arSorter[$city['country_name']])) {
                $arSorter[$city['country_name']] = 1;
            } else {
                $arSorter[$city['country_name']]++;
            }
        }

        //У кого городов больше всех тот первым идет
        arsort($arSorter);

        $sortedAlphCitiesList = [];
        foreach ($arSorter as $country => $weight) {
            // И тут перемещает страны с городами в отсортированый по количеству массив стран
            $sortedAlphCitiesList[$country] = $alphCitiesList[$country];
        }

        return $sortedAlphCitiesList;
    }

    /**
     * Видимость на сайте
     * - Видно
     * - Не видно
     */
    const VISIBLE = 1;

    const INVISIBLE = 0;

    /**
     * @return array Массив доступных данных видимости апартаментов на сайте
     */
    public static function getVisibleArray()
    {
        return [
            self::VISIBLE => [
                'label' => 'Видимый',
                'style' => 'color: green',
            ],
            self::INVISIBLE => [
                'label' => 'Невидимый',
                'style' => 'color: red',
            ],
        ];
    }

    /**
     * Статус
     * - Активный
     * - Не активный
     */
    const ACTIVE = 1;

    const INACTIVE = 0;

    const BLOCKED = 2;

    /**
     * @return array Массив доступных данных статуса апартаментов
     */
    public static function getStatusArray()
    {
        return [
            self::ACTIVE => [
                'label' => 'Проверено',
                'style' => 'color: green',
            ],
            self::INACTIVE => [
                'label' => 'На модерации',
                'style' => 'color: red',
            ],
            self::BLOCKED => [
                'label' => 'Заблокировано',
                'style' => 'color: blue',
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function getRemontList()
    {
        return TotalApartment::getRemontArray();
    }

    /**
     * @inheritdoc
     */
    public function getGuestsLimitList()
    {
        return TotalApartment::getGuestsLimitArray();
    }

    /**
     * Текстовое представление типа ремонта
     * @return mixed|null
     */
    public function getRemontName()
    {
        if ($this->remont) {
            $name = ArrayHelper::getValue($this->remontList, $this->remont);

            return trim(sprintf('%s %s', $name, $this->remont === 3 ? '' : 'ремонт'));
        }

        return null;
    }

    /**
     * @return string Текстовое представление кол-ва комнат
     */
    public function getRoomsName()
    {
        if ($this->total_rooms) {
            return ArrayHelper::getValue($this->roomsList, $this->total_rooms);
        }

        return null;
    }

    /**
     * Максимальное кол-во гостей
     * @return int
     */
    public function getGuestsLimit(): int
    {
        return $this->guests_limit ?: 2;
    }

    /**
     * @inheritdoc
     */
    public function getRoomsList()
    {
        return TotalApartment::getRoomsArray();
    }

    /**
     * @inheritdoc
     */
    public function getBedsList()
    {
        return TotalApartment::getBedsArray();
    }

    /**
     * @inheritdoc
     */
    public function getMetroWalkList()
    {
        return TotalApartment::getMetroWalkArray();
    }

    public function getMetroWalkText()
    {
        return ArrayHelper::getValue($this->metroWalkList, $this->metro_walk);
    }

    /**
     * WARNING! не соединять с другими таблицами с помощью Join
     * @return \yii\db\ActiveQuery
     */
    public function getOrderedImages()
    {
        return $this->hasMany(Image::class, ['apartment_id' => 'apartment_id'])->orderBy('sort ASC')->inverseOf('apartment');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFacilities()
    {
        return $this->hasMany(Facility::class, ['facility_id' => 'facility_id'])
            ->viaTable('{{%partners_apartments_facilities}}', ['apartment_id' => 'apartment_id'])
            ->innerJoin('{{%partners_apartments_facilities}} paf', 'paf.facility_id = {{%partners_facilities%}}.facility_id and paf.apartment_id = ' . $this->apartment_id)
            ->select('{{%partners_facilities%}}.*, paf.value');
    }

    /**
     * @inheritdoc
     */
    public function hasReserved($date_from, $date_to)
    {
        return Calendar::hasReserved($this->apartment_id, $date_from, $date_to);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        //Определяем координаты по адресу
        /* TODO: попытка определить координаты по адресу, пакет устарел и не работает с новым Yii2
        ---------------------------------------------------------------------------------------------
        // Определяем координаты по адресу
        $geo = new \vitalik74\geocode\Geocode();
        $fullAddress = $this->city->country->name . ' ' . $this->city->name . ' ' . $this->address;

        $api = $geo->get($fullAddress, ['kind' => 'house'], '520b7c80-9741-4818-82a7-760d250c2d88');
        $pos = null;
        if (isset($api['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
            $pos = $api['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
        }
        $longitude = null;
        $latitude = null;
        if ($pos) {
            $coord = @explode(' ', $pos);
            $longitude = isset($coord[0]) ? $coord[0] : null;
            $latitude = isset($coord[1]) ? $coord[1] : null;
        }

        if (!$longitude || !$latitude) {
            throw new ErrorException('Failed to determine the coordinates. $longitude: ' . $longitude . ' $latitude: ' . $latitude);
        }

        $this->latitude = $latitude;
        $this->longitude = $longitude;
        ---------------------------------------------------------------------------------------------
        */

        $this->open_contacts = 1;

        return true;
    }

    /**
     * Проверка существования удобства по алиасу
     * @param string $alias
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isFacilityExist(string $alias): bool
    {
        if (!$this->apartment_id) {
            return false;
        }

        return $this->getFacility($alias) ? true : false;
    }

    public function getFacility(string $alias)
    {
        if (!$this->apartment_id) {
            return null;
        }

        foreach ($this->getFacilities()->all() as $facility) {
            if ($facility->alias === $alias) {
                return $facility;
            }
        }

        return null;
    }

    public function getFacilitiesByType(bool $isExtra = false)
    {
        return $this->getFacilities()->andWhere(['is_extra' => $isExtra ? 1 : 0])->all();
    }

    public function getTimeToMetroInMinutes($transportType = 0)
    {
        $transportSpeed = [ // км/ч
            0 => 4,  // пешеход
            1 => 30, // такси
            2 => 20, // общественный
        ];

        return !$this->metro_walk ? '-' : round($this->metro_walk  / $transportSpeed[$transportType] * 60) . ' МИН.' ;
    }
}
