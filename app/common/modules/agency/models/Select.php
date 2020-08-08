<?php

namespace common\modules\agency\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\modules\geo\models\Metro;
use common\modules\realty\models\RentType;
use common\modules\agency\traits\ModuleTrait;
use common\modules\realty\models\Apartment as ApartmentConfig;

/**
 * Заявки на подберем квартиру
 * @package common\modules\agency\models
 */
class Select extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * Статусы зявок
     * PROCESSED - Обработанная
     * UNPROCESSED - Необработанная
     */
    const PROCESSED = 1;

    const UNPROCESSED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_apartment_select}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'apartment_select_id' => '№',
            'name' => 'Ф.И.О.',
            'phone' => 'Телефон',
            'phone2' => 'Дополнительный телефон',
            'email' => 'EMAIL',
            'rent_types' => 'Типы аренды',
            'rooms' => 'Кол-во комнат',
            'description' => 'Дополнительная информация',
            'metro' => 'Станции метро',
            'status' => 'Статус заявки',
            'rent_types_array' => 'Типы аренды',    // Оболочка презентующая поле rent_type
            'metro_array' => 'Станции метро',    // Оболочка презентующая поле metro
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * Список статусов
     * @return array
     */
    public static function getStatusesArray()
    {
        return [
            self::PROCESSED => [
                'label' => 'Обработанная',
                'style' => 'color: green',
            ],
            self::UNPROCESSED => [
                'label' => 'Необработанная',
                'style' => 'color: red',
            ],
        ];
    }

    /**
     * Возвращает массив "Типов аренды"
     * @return array презентирующий "rent_types" атрибут
     */
    public function getRent_types_array()
    {
        return Json::decode($this->rent_types);
    }

    /**
     * Возвращает массив "Метро станций"
     * @return array презентирующий "metro" атрибут
     */
    public function getMetro_array()
    {
        return Json::decode($this->metro);
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getRentTypesList()
    {
        return RentType::rentTypeslist();
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getMetroStations()
    {
        $metro = Metro::find()
            ->select(['metro_id', 'name'])
            ->where(['city_id' => 4400])
            ->asArray()
            ->all();

        return ArrayHelper::map($metro, 'metro_id', 'name');
    }

    /**
     * Список доступных вариантов количества комнат
     * @return array
     */
    public static function getRoomsList()
    {
        return ApartmentConfig::getRoomsArray();
    }

    /**
     * Текстовое представление обозначения на главной странице или нет
     * @return string
     */
    public function getStatusText()
    {
        return Yii::$app->BasisFormat->helper('Status')->getItem($this->statusesArray, $this->status);
    }
}
