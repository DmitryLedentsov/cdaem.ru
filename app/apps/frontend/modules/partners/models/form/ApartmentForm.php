<?php

namespace frontend\modules\partners\models\form;

use common\modules\geo\models\City;
use common\modules\geo\models\Metro;
use common\modules\realty\models\Apartment as TotalApartment;
use common\modules\partners\models\MetroStations;
use frontend\modules\partners\models\Apartment;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models\Image;
use yii\helpers\Html;
use Yii;

/**
 * @inheritdoc
 */
class ApartmentForm extends Apartment
{
    public $metro_array;
    public $translit;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'date_update',
                ],
                "value" => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'BlameableBehavior' => [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->visible = self::VISIBLE;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return array_merge($labels, [
            'city' => 'Город',
            'metro_array' => 'Метро',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'user-create' => ['city_id', 'address', 'apartment', 'floor', 'total_rooms', 'total_area', 'beds', 'remont', 'metro_walk', 'description', 'visible', 'metro_array'],
            'user-update' => ['city_id', 'address', 'apartment', 'floor', 'total_rooms', 'total_area', 'beds', 'remont', 'metro_walk', 'description', 'visible', 'metro_array'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'user-create' => self::OP_ALL,
            'user-update' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['city_id', 'required'],
            ['city_id', 'integer'],
            ['city_id', 'exist', 'targetClass' => '\common\modules\geo\models\City', 'targetAttribute' => 'city_id'],

            ['address', 'required'],
            ['address', 'string'],

            ['visible', 'required'],
            ['visible', 'boolean'],

            ['apartment', 'integer', 'min' => 1],

            ['floor', 'integer', 'min' => 1],

            ['total_area', 'integer', 'min' => 1],

            ['total_rooms', 'required'],
            ['total_rooms', 'in', 'range' => array_keys(TotalApartment::getRoomsArray())],

            ['beds', 'required'],
            ['beds', 'in', 'range' => array_keys(TotalApartment::getBedsArray())],

            ['remont', 'required'],
            ['remont', 'in', 'range' => array_keys($this->remontList)],

            ['metro_walk', 'in', 'range' => array_keys(TotalApartment::getMetroWalkArray())],

            ['description', 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            if (!$this->metro_walk) {
                $this->metro_walk = 0;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->scenario == 'user-create') {
                $this->status = self::INACTIVE;
            }

            if ($this->scenario == 'user-update') {
                $this->status = self::INACTIVE;
            }

            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rus2translit($text)
    {
        // Русский алфавит
        $rus_alphabet = array(
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
            'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
            'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
            'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', ','
        );

        // Английская транслитерация
        $rus_alphabet_translit = array(
            'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'I',
            'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
            'H', 'C', 'CH', 'SH', 'SH', '`', 'Y', '`', 'E', 'IU', 'IA',
            'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'i',
            'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
            'h', 'c', 'ch', 'sh', 'sh', '`', 'y', '`', 'e', 'iu', 'ia', '_', '_', '_'
        );

        return str_replace($rus_alphabet, $rus_alphabet_translit, $text);
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Создание объявлений для апартаментов при создании
        if ($this->scenario == 'user-create') {

            // Сохраняем объявления
            foreach ($this->adverts->newAdverts as $advert) {
                $newAdvert = new AdvertForm();
                $newAdvert->apartment_id = $this->apartment_id;
                $newAdvert->rent_type = $advert['rent_type_id'];
                $newAdvert->price = $advert['price'];
                $newAdvert->currency = $advert['currency'];
                $newAdvert->top = 0;
                $newAdvert->selected = 0;
                $newAdvert->position = 1;
                $newAdvert->save(false);
            }
        }


        // Создание объявлений для апартаментов при редактировании
        if ($this->scenario == 'user-update') {

            $currentAdvertsByRentType = [];

            print_r($this->adverts->newAdverts);

            // Фиксируем новые изменения
            foreach ($this->adverts->newAdverts as $newAdvert) {

                $advert = Advert::find()
                    ->where('rent_type = :rent_type_id', [':rent_type_id' => $newAdvert['rent_type_id']])
                    ->andWhere('apartment_id = :apartment_id', [':apartment_id' => $this->apartment_id])
                    ->one();

                if (!$advert) {
                    $advert = new Advert();
                    $advert->rent_type = $newAdvert['rent_type_id'];
                    $advert->apartment_id = $this->apartment_id;
                    $advert->top = 0;
                    $advert->selected = 0;
                    $advert->position = 1;
                }

                $advert->price = $newAdvert['price'];
                $advert->currency = $newAdvert['currency'];
                $advert->save(false);

                $currentAdvertsByRentType[] = $advert->rent_type;
            }


            // Удалить все ненужные объявления
            $oldAdverts = Advert::find()
                ->where(['NOT IN', 'rent_type', $currentAdvertsByRentType])
                ->andWhere('apartment_id = :apartment_id', [':apartment_id' => $this->apartment_id])
                ->all();

            if ($oldAdverts) {
                foreach ($oldAdverts as $oldAdvert) {
                    $oldAdvert->delete();
                }
            }
        }

        // Определяем необходимо ли установить изображение по умолчанию
        $setDefaultImage = false;
        if ($this->scenario == 'user-update') {
            if (!Image::find()
                ->andWhere(['apartment_id' => $this->apartment_id])
                ->andWhere('default_img = 1')
                ->count()) {
                $setDefaultImage = true;
            }
        }

        $translit = $this->rus2translit($this->address);

        // Сохраняем изображения
        foreach ($this->images->files as $key => $file) {
            if ($imageName = $this->images->upload($file, true, true, $translit)) {
                $image = new Image();
                $image->apartment_id = $this->apartment_id;
                $image->preview = $imageName;
                $image->review = $imageName;
                $image->default_img = ($setDefaultImage && $key === 0) ? 1 : 0;
                $image->sort = $key + 1;
                $image->save(false);
            }
        }


        // Работа с метро
        // [Москва]
        if (City::findOne($this->city_id)->hasMetro()) {
            if ($this->scenario == 'user-update') {
                MetroStations::deleteAll(['apartment_id' => $this->apartment_id]);
            }
            if ($metroAll = Metro::findAll($this->metro_array)) {
                foreach ($metroAll as $metro) {
                    $metroStations = new MetroStations();
                    $metroStations->apartment_id = $this->apartment_id;
                    $metroStations->metro_id = $metro->metro_id;
                    $metroStations->save(false);
                }
            }
        }

        return true;
    }
}
