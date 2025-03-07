<?php

namespace common\modules\partners\models\frontend\form;

use yii\helpers\ArrayHelper;
use common\modules\geo\models\City;
use common\modules\geo\models\Metro;
use common\modules\geo\models\Region;
use common\modules\partners\models\Facility;
use common\modules\partners\models\frontend\Image;
use common\modules\partners\models\frontend\Advert;
use common\modules\partners\models\frontend\Apartment;
use common\modules\partners\models\frontend\MetroStations;
use common\modules\realty\models\Apartment as TotalApartment;

class ApartmentForm extends Apartment
{
    public array $metro_array = [];

    public string $translit;

    public string $city_name;

    public string $region_name;

    public array $exist_image_ids = [];

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
            'user-create' => ['city_name', 'region_name', /*'city_id',*/ 'address', 'apartment', 'floor', 'total_rooms', 'total_area', 'sleeping_place', 'beds', 'remont', 'metro_walk', 'description', 'visible', 'metro_array', 'building_type', 'number_floors', 'number_entrances', 'latitude', 'longitude'],
            'user-update' => ['city_name', 'region_name', /*'city_id',*/ 'address', 'apartment', 'floor', 'total_rooms', 'total_area', 'sleeping_place', 'beds', 'remont', 'metro_walk', 'description', 'visible', 'metro_array', 'building_type', 'number_floors', 'number_entrances', 'latitude', 'longitude', 'exist_image_ids'],
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
            // ['city_id', 'required'],
            // ['city_id', 'integer'],
            // ['city_id', 'exist', 'targetClass' => \common\modules\geo\models\City::class, 'targetAttribute' => 'city_id'],

            ['city_name', 'required'],
            ['city_name', 'string'],

            ['region_name', 'required'],
            ['region_name', 'string'],

            ['address', 'required'],
            ['address', 'string'],

            ['visible', 'required'],
            ['visible', 'boolean'],

            ['apartment', 'integer', 'min' => 1],

            ['total_area', 'required'],
            ['total_area', 'integer', 'min' => 1, 'max' => 1000],

            ['floor', 'required'],
            ['floor', 'integer', 'min' => -10, 'max' => 1000],

            ['total_rooms', 'required'],
            ['total_rooms', 'in', 'range' => array_filter(array_keys(TotalApartment::getRoomsArray()))], // все кроме 0 => 'Выбрать'

            ['beds', 'required'],
            ['beds', 'in', 'range' => array_keys(TotalApartment::getBedsArray())],

            ['sleeping_place', 'required'],
            ['sleeping_place', 'in', 'range' => array_keys(TotalApartment::getSleepingPlacesArray())],

            ['remont', 'required'],
            ['remont', 'in', 'range' => array_filter(array_keys($this->remontList))], // все кроме 0 => 'Выбрать'

            ['metro_walk', 'in', 'range' => array_keys(TotalApartment::getMetroWalkArray())],

            ['description', 'string', 'max' => 2000],

            ['building_type', 'in', 'range' => array_keys(TotalApartment::getBuildingTypeArray())],

            ['number_entrances', 'integer', 'min' => 0, 'max' => 65535],

            // ['number_floors', 'required'],
            ['number_floors', 'integer', 'min' => 0, 'max' => 1000],

            ['latitude', 'double'],
            ['longitude', 'double'],
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

                $regionName = $this->region_name;
                $region = Region::findOne(["name" => $regionName]);
                $isNewRegion = false;

                if (!$region) {
                    $isNewRegion = true;
                    $region = new Region([
                        'country_id' => 3159, // Россия
                        'name' => $regionName,
                        'city_id' => 0 // Самый большой город?
                    ]);

                    $region->save(false);
                }

                $city = City::findByName($this->city_name);

                if (!$city) {
                    $city = (new City([
                        'country_id' => 3159, // Россия
                        'region_id' => $region->region_id,
                        'name' => $this->city_name,
                        'is_popular' => 0
                    ]));

                    $city->save(false);
                } else {
                    // Обновляем регион если создали его, а город уже есть.
                    if ($isNewRegion) {
                        $city->region_id = $region->region_id;
                        $city->save(false);
                    }
                }

                $this->city_id = $city->city_id;
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
        $rus_alphabet = [
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
            'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
            'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
            'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', ','
        ];

        // Английская транслитерация
        $rus_alphabet_translit = [
            'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'I',
            'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
            'H', 'C', 'CH', 'SH', 'SH', '`', 'Y', '`', 'E', 'IU', 'IA',
            'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'i',
            'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
            'h', 'c', 'ch', 'sh', 'sh', '`', 'y', '`', 'e', 'iu', 'ia', '_', '_', '_'
        ];

        return str_replace($rus_alphabet, $rus_alphabet_translit, $text);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->scenario === 'user-update-contact-status') {
            return true;
        }

        // Создание объявлений для апартаментов при создании
        if ($this->scenario === 'user-create') {
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
        if ($this->scenario === 'user-update') {
            $currentAdvertsByRentType = [];

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

        $apartment = \common\modules\partners\models\Apartment::findById($this->apartment_id);

        // Очищаем удалённые и перезалитые изображнеия
        foreach ($apartment->getOrderedImages()->all() as $image) {
            if (!in_array($image->image_id, $this->exist_image_ids)) {
                $image->deleteWithFiles();
            }
        }

        // Сохраняем изображения
        $translit = $this->rus2translit($this->address);

        foreach ($this->images->files as $key => $file) {
            if ($imageName = $this->images->upload($file, true, true, $translit)) {
                $image = new Image();
                $image->apartment_id = $this->apartment_id;
                $image->preview = $imageName;
                $image->review = $imageName;
                $image->save(false);
            }
        }

        // Устанавливаем изображение по умолчанию и сортируем
        $i = 0;
        foreach ($apartment->getOrderedImages()->all() as $image) {
            $image->default_img = $i === 0 ? 1 : 0;
            $image->sort = $i + 1;
            $image->save(false);
            $i++;
        }

        // Работа с метро
        if (City::findOne($this->city_id)->hasMetro() && $this->metro_array) {
            if ($this->scenario === 'user-update') {
                MetroStations::deleteAll(['apartment_id' => $this->apartment_id]);
            }

            $metroAll = Metro::findAll(['city_id' => $this->city_id, 'name' => $this->metro_array]);

            foreach ($metroAll as $metro) {
                $metroStations = new MetroStations();
                $metroStations->apartment_id = $this->apartment_id;
                $metroStations->metro_id = $metro->metro_id;
                $metroStations->save(false);
            }
        }

        // Сохраняем удобства

        // Удаляем все старые удобства
        // todo почему не работает?
        // Facility::deleteAll(['apartment_id' => $this->apartment_id]);
        \Yii::$app->db->createCommand('delete from {{%partners_apartments_facilities}} paf where paf.apartment_id = :apartment_id', ['apartment_id' => $this->apartment_id])->query();

        foreach ($this->facilities->facilities as $facility) {
            /** @var Facility $facility */
            $value = $facility->getValue();

            if (is_array($value)) {
                $value = serialize($value);
            }

            $facility->link('apartments', $this, ['value' => $value]);
        }

        return true;
    }
}
