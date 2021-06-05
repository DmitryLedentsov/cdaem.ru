<?php

namespace common\modules\agency\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\modules\geo\models\Metro;
use common\modules\realty\models\RentType;
use common\modules\agency\traits\ModuleTrait;
use common\modules\realty\models\Apartment as TotalApartment;

/**
 * Заявки на Хочу сдать квартиру
 * @package common\modules\agency\models
 */
class WantPass extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_apartment_want_pass}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'apartment_want_pass_id' => '№',
            'name' => 'Ф.И.О.',
            'phone' => 'Телефон',
            'phone2' => 'Дополнительный телефон',
            'email' => 'EMAIL',
            'rent_types' => 'Типы аренды',
            'rooms' => 'Кол-во комнат',
            'description' => 'Дополнительная информация',
            'address' => 'Адрес',
            'metro' => 'Станции метро',
            'images' => 'Изображения',
            'status' => 'Статус заявки',
            'rent_types_array' => 'Типы аренды',    // Оболочка презентующая поле rent_type
            'metro_array' => 'Станции метро',    // Оболочка презентующая поле metro
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * Статусы зявок
     * PROCESSED - Обработанная
     * UNPROCESSED - Необработанная
     */
    const PROCESSED = 1;

    const UNPROCESSED = 0;

    /**
     * Список Статусов
     * @return array
     */
    public static function getStatusesArray(): array
    {
        return [
            self::PROCESSED => [
                'label' => 'Обработанная',
                'color' => 'green',
            ],
            self::UNPROCESSED => [
                'label' => 'Необработанная',
                'color' => 'red',
            ],
        ];
    }

    /**
     * Удаляет записи из БД вместе с файлами из сервера
     *
     * @param string|array $condition
     * Пожалуйста обратитесь к [[ActiveRecord::deleteAll()]] чтобы узнать как назначать параметры.
     * @param array $params
     * @return integer количество удаленных записей
     */
    public static function deleteAllWithFiles($condition = '', $params = [])
    {
        $wantPasses = self::find()
            ->where($condition, $params)
            ->all();
        $count = 0;
        foreach ($wantPasses as $wantPass) {
            foreach ($wantPass->files_array as $file) {
                @unlink(Yii::getAlias(Yii::$app->getModule('agency')->imagesWantPassPath) . '/' . $file);
            }
            $count += $wantPass->delete();
        }

        return $count;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $images = Json::decode($this->images);
        if (is_array($images)) {
            foreach ($images as $image) {
                @unlink(Yii::getAlias($this->module->imagesWantPassPath) . '.' . $image);
            }
        }
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
     * Возвращает массив SRC картинок
     * @return array
     */
    public function getImages_array()
    {
        //TODO: заменить /uploads/agency/wantpass/ на переменную из конфига
        $images = Json::decode($this->images);

        if (!is_array($images)) {
            return [];
        }

        foreach ($images as &$image) {
            $image = Yii::$app->params['siteDomain'] . '/uploads/agency/wantpass/' . $image;
        }

        return $images;
    }

    /**
     * Возвращает массив имен файлов всех картинок этой заявки
     * @return array
     */
    public function getFiles_array()
    {
        return Json::decode($this->images);
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getRentTypesList(): array
    {
        return RentType::rentTypeslist();
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getMetroStations(): array
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
    public static function getRoomsList(): array
    {
        return TotalApartment::getRoomsArray();
    }

    /**
     * Текстовое представление обозначения на главной странице или нет
     * @return string
     */
    public function getStatusText(): string
    {
        return Yii::$app->BasisFormat->helper('Status')->getItem($this->statusesArray, $this->status);
    }
}
