<?php

namespace common\modules\realty\models;

use yii\db\ActiveRecord;

/**
 * Данные апартаментов
 * @package common\modules\realty\models
 */
class Apartment extends ActiveRecord
{
    /**
     * @return array Список валюты
     */
    public static function getCurrencyArray(): array
    {
        return [
            1 => 'RUB',
            2 => 'USD',
            3 => 'EUR',
        ];
    }

    /**
     * @return array Список кол-во квартир
     */
    public static function getRoomsArray(): array
    {
        return [
            1 => '1 комната',
            2 => '2 комнаты',
            3 => '3 комнаты',
            4 => '4 комнаты',
            5 => '5 комнат',
            6 => '6 и более',
        ];
    }

    /**
     * @return array Список типов ремонта
     */
    public static function getRemontArray(): array
    {
        return [
            1 => 'Старый',
            2 => 'Косметический',
            3 => 'Евроремонт',
            4 => 'Дизайнерский',
        ];
    }

    /**
     * @return array лимит гостей
     */
    public static function getGuestsLimitArray(): array
    {
        return [
            1,
            2,
            3,
            4,
            5,
            6,
            7,
        ];
    }

    /**
     * @return array Список кол-во кроватей
     */
    public static function getBedsArray(): array
    {
        return [
            1 => '1 кровать',
            2 => '2 кровати',
            3 => '3 кровати',
            4 => '4 кровати',
            5 => '5 кроватей',
            6 => '6 и более',
        ];
    }

    /**
     * @return array Список кол-во спальных мест
     */
    public static function getSleepingPlacesArray(): array
    {
        return [
            1 => '1 место',
            2 => '2 места',
            3 => '3 места',
            4 => '4 места',
            5 => '5 мест',
            6 => '6 и более',
        ];
    }

    /**
     * Список доступных выборов для дальности от метро
     * @return array
     */
    public static function getMetroWalkArray(): array
    {
        return [
            0 => 'Нет метро',
            -1 => 'Не важно',
            1 => '1 минута',
            2 => '2 минуты',
            3 => '3 минуты',
            4 => '4 минуты',
            5 => '5 минут',
            6 => '6 минут',
            7 => '7 минут',
            8 => '8 минут',
            9 => '9 минут',
            10 => '10 минут',
            11 => '11 минут',
            12 => '12 минут',
            13 => '13 минут',
            14 => '14 минут',
            15 => '15 минут',
            16 => '16 минут',
            17 => '17 минут',
            18 => '18 минут',
            19 => '19 минут',
            20 => '20 минут',
            29 => 'до 30 минут',
            31 => 'Более 30 минут',
            55 => 'Около 5 минут транспортом',
            65 => 'Около 15 минут транспортом',
            80 => 'Около 30 минут транспортом',
            90 => 'Час и более транспортом'
        ];
    }

    /**
     * Список доступных выборов для дальности от метро
     * @return array
     */
    public static function getMetroWalkBaseArray(): array
    {
        return [
            1 => 'Пешком',
            2 => 'Транспортом',
        ];
    }

    /**
     * Массив доступных значений Этаж
     * @return array
     */
    public static function getFloorArray(): array
    {
        return [
            1 => 'Только первый',
            2 => 'Не первый',
            3 => 'Не последний',
        ];
    }

    /**
     * Массив доступных значений Безопасность
     * @return array
     */
    public static function getSafetyArray(): array
    {
        return [
            1 => 'Домофон',
            2 => 'Сигнализация',
            3 => 'Консьерж',
            4 => 'Охрана',
        ];
    }

    /**
     * Массив доступных значений Отопление
     * @return array
     */
    public static function getHeatingArray(): array
    {
        return [
            0 => 'Нет',
            1 => 'Обогреватель',
            2 => 'Центральное',
            3 => 'Печное',
            4 => 'Газовое',
        ];
    }

    /**
     * Массив доступных значений Покрытие пола
     * @return array
     */
    public static function getFloorCoveringArray(): array
    {
        return [
            // 0 => 'Нет',
            1 => 'Ламинат',
            2 => 'Линолеум',
            3 => 'Паркет',
            4 => 'Ковролин',
        ];
    }

    /**
     * Массив доступных значений Санузлы
     * @return array
     */
    public static function getBathroomArray(): array
    {
        return [
            1 => 'Нет',
            2 => '1',
            3 => '2',
            4 => '3',
            5 => '4',
            6 => '5',
            7 => '6',
            8 => '7+',
        ];
    }

    /**
     * Массив доступных значений Тип здания
     * @return array
     */
    public static function getBuildingTypeArray(): array
    {
        return [
            1  => 'Кирпичное',
            2  => 'Панельное',
            3  => 'Сталинское',
            4  => 'Хрущёвка',
            5  => 'Деревянное',
            6  => 'Монолитнокирпичное',
            7  => 'Пнельно-кирпичное',
            8  => 'Каркасно-блочное',
            9  => 'Щитовое',
            10 => 'Брус',
            11 => 'Пеноблоки',
        ];
    }
}
