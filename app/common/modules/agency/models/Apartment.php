<?php

namespace common\modules\agency\models;

use common\modules\agency\models\query\ApartmentsQuery;
use common\modules\realty\models\Apartment as TotalApartment;
use common\modules\agency\traits\ModuleTrait;
use common\modules\agency\models\Reservation;
use common\modules\geo\models\Districts;
use common\modules\geo\models\Metro;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Апартаменты
 * @package common\modules\agency\models
 */
class Apartment extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_apartments}}';
    }

    /**
     * @return ApartmentsQuery
     */
    public static function find()
    {
        return new ApartmentsQuery(get_called_class());
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
            'closest_city_id' => 'Ближайший город',
            'address' => 'Адрес',
            'apartment' => 'Номер квартиры',
            'district1' => 'Округ',
            'district2' => 'Округ 2',
            'floor' => 'Этаж',
            'total_rooms' => 'Количество комнат',
            'total_area' => 'Общая площадь',
            'beds' => 'Кол-во спальных мест',
            'visible' => 'Отображается',
            'remont' => 'Ремонт',
            'metro_walk' => 'Пешком к метро',
            'description' => 'Описание',
            'date_create' => 'Дата создания',
            'date_update' => 'Дата редактирования',
        ];
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
        return $this->hasOne(\common\modules\geo\models\City::class, ['city_id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainDistrict()
    {
        return $this->hasOne(\common\modules\geo\models\Districts::class, ['id' => 'district1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemiDistrict()
    {
        return $this->hasOne(\common\modules\geo\models\Districts::class, ['id' => 'district2']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetroStations()
    {
        return $this->hasMany(ApartmentMetroStations::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetroStation()
    {
        return $this->hasOne(ApartmentMetroStations::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservations()
    {
        return $this->hasMany(Reservation::class, ['apartment_id' => 'apartment_id']);
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
    public function getTitleImage()
    {
        return $this->hasOne(Image::class, ['apartment_id' => 'apartment_id'])
            ->orderBy('default_img DESC')->inverseOf('apartment');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getAlternateTitleImage()
    {
        return $this->hasOne(Image::class, ['apartment_id' => 'apartment_id'])
            ->andWhere(['default_img' => 0]);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['apartment_id' => 'apartment_id'])->inverseOf('apartment');
    }

    /**
     * WARNING! не соеденять с другими таблицами с помощью Join
     * @return \yii\db\ActiveQuery
     */
    public function getOrderedImages()
    {
        return $this->hasMany(Image::class, ['apartment_id' => 'apartment_id'])->orderBy('sort ASC')->inverseOf('apartment');
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // Удаляем изображения из базы и диска
        if (parent::beforeDelete()) {
            Image::deleteAllWithFiles(['apartment_id' => $this->apartment_id]);
            return true;
        }

        return false;
    }

    /**
     * Возвращает адрес заглавной картинки
     * Для уменьшения количества запросов к БД, рекомендуется использовать вместе с реляцией titleImage
     * @return string
     */
    public function getTitleImageSrc()
    {
        $folder = Yii::getAlias(Yii::$app->getModule('agency')->imagesPath);
        if ($this->titleImage) {
            $file = $folder . '/' . $this->titleImage->review;
            if (file_exists($file)) {
                return Yii::$app->params['siteDomain'] . Yii::$app->getModule('agency')->previewImagesUrl . '/' . $this->titleImage->preview;
            }
        }

        return Yii::$app->params['siteDomain'] . Yii::$app->getModule('agency')->defaultImageSrc;
    }

    /**
     * Возвращает Title заглавной картинки этого апартамента
     * @return string
     */
    public function getTitleImageTitle()
    {
        if ($this->titleImage) {
            return $this->titleImage->frontTitle;
        }

        $title = "Фото " . $this->city->name;
        $title .= ", ул. " . $this->address;
        $title .= $this->apartment ? ', кв. ' . $this->apartment : '';

        return $title;
    }

    /**
     * Возвращает Alt заглавной картинки этого апартамента
     * @return string
     */
    public function getTitleImageAlt()
    {
        if ($this->titleImage) {
            return $this->titleImage->frontAlt;
        }

        $alt = $this->city->name;
        $alt .= ", ул. " . $this->address;
        $alt .= $this->apartment ? ', кв. ' . $this->apartment : '';

        return $alt;
    }

    /**
     * Видимость на сайте
     * - Видно
     * - Не видно
     */
    const VISIBLE = 1;
    const INVISIBLE = 0;

    /**
     * Массив доступных данных видимости апартаментов на сайте
     * @return array
     */
    public static function getVisibleArray()
    {
        $statuses = [
            self::VISIBLE => [
                'label' => 'Видимый',
                'style' => 'color: green',
            ],
            self::INVISIBLE => [
                'label' => 'Невидимый',
                'style' => 'color: red',
            ],
        ];

        return $statuses;
    }

    /**
     * @inheritdoc
     */
    public static function getRoomsList()
    {
        return TotalApartment::getRoomsArray();
    }

    /**
     * @inheritdoc
     */
    public static function getRemontList()
    {
        return TotalApartment::getRemontArray();
    }

    /**
     * Текстовое представление типа ремонта
     * @return string
     */
    public function getRemontName()
    {
        if ($this->remont) {
            return ArrayHelper::getValue($this->remontList, $this->remont);
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
     * @return array Массив типов ремонта
     */
    public static function getDistrictsList()
    {
        $districts = Districts::find()->asArray()->all();
        return ArrayHelper::map($districts, 'id', 'district_name');
    }

    /**
     * Список возможных метро станций
     * @return array
     */
    public static function getMetroList()
    {
        return Metro::getMskMetroArray();
    }

    /**
     * Список возможных дальностей к метро
     * @return array
     */
    public static function getMetroWalkList()
    {
        return TotalApartment::getMetroWalkArray();
    }

    /**
     * Дальность от метро этой резервации в текстовом виде
     * @return mixed
     */
    public function getMetroWalkText()
    {
        return ArrayHelper::getValue($this->metroWalkList, $this->metro_walk);
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

        return true;
    }

}