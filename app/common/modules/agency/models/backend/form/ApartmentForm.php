<?php

namespace common\modules\agency\models\backend\form;

use common\modules\realty\models\Apartment as TotalApartment;
use common\modules\agency\models\ApartmentMetroStations;
use common\modules\agency\models\Apartment;
use common\modules\agency\models\Image;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Apartment Form
 * @package common\modules\agency\models\backend\form
 */
class ApartmentForm extends \yii\base\Model
{
    public $apartment_id;
    public $user_id;
    public $city_id;
    public $closest_city_id;
    public $address;
    public $apartment;
    public $district1;
    public $district2;
    public $floor;
    public $total_rooms;
    public $total_area;
    public $beds;
    public $visible;
    public $remont;
    public $metro_walk;
    public $description;
    public $date_create;
    public $date_update;
    public $orderedImages = [];
    public $files;
    public $metroStations;
    public $translit;

    /**
     * Список ID станций метро, принадлежащих этому апартаменту (agency_metro_stations.id == metro.metro_id)
     * (не спутать с agency_metro_stations.id)
     * @var array
     */
    private $_metroStationsArray;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Apartment())->attributeLabels(), [
            'files' => 'Выберите изображения',
            'metroStationsArray' => 'Список метро',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['city_id', 'closest_city_id', 'address', 'apartment', 'district1', 'district2', 'floor', 'total_rooms', 'total_area', 'beds', 'visible', 'remont', 'metro_walk', 'description', 'files', 'metroStationsArray'],
            'update' => ['user_id', 'city_id', 'closest_city_id', 'address', 'apartment', 'district1', 'district2', 'floor', 'total_rooms', 'total_area', 'beds', 'visible', 'remont', 'metro_walk', 'description', 'date_create', 'date_update', 'files', 'metroStationsArray']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'city_id', 'closest_city_id', 'apartment', 'district1', 'district2', 'floor', 'total_rooms', 'total_area', 'visible', 'remont', 'metro_walk'], 'integer'],
            ['user_id', 'exist', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'id'],
            [['city_id', 'closest_city_id'], 'exist', 'targetClass' => '\common\modules\geo\models\City', 'targetAttribute' => 'city_id'],
            [['district1', 'district2'], 'exist', 'targetClass' => '\common\modules\geo\models\Districts', 'targetAttribute' => 'id'],
            [['visible'], 'boolean'],
            ['remont', 'in', 'range' => array_keys(Apartment::getRemontList())],
            ['beds', 'integer'],
            ['metroStationsArray', 'default', 'value' => []],
            ['metroStationsArray', 'each', 'rule' => ['in', 'range' => array_keys(Apartment::getMetroList())]],
            [['address'], 'string', 'max' => 255],
            [['description'], 'string'],

            [['files'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 10],

            // required on %scenarios%
            [['city_id', 'address', 'district1', 'floor', 'total_rooms', 'total_area', 'visible', 'remont', 'metro_walk', 'description', 'images', 'metroStationsArray', 'beds'], 'required', 'on' => 'create'],
            [['user_id', 'city_id', 'address', 'district1', 'floor', 'total_rooms', 'total_area', 'visible', 'remont', 'metro_walk', 'description', 'metroStationsArray', 'beds'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->files = UploadedFile::getInstances($this, 'files');
            return true;
        }
        return false;
    }

    /**
     * Создать
     *
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $model = new Apartment();
            $model->setAttributes($this->getAttributes(), false);
            $model->user_id = Yii::$app->user->id;
            $model->date_create = date('Y-m-d H:i:s');
            $model->date_update = $model->date_create;

            if (!$model->save(false)) {
                throw new \Exception('Apartment not save');
            }

            $this->apartment_id = $model->apartment_id;

            if (!$this->updateMetroStations()) {
                throw new \Exception('Apartment not update metro stations');
            }

            if (!$this->createImages()) {
                throw new \Exception('Apartment not create images');
            }

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            //return false;
            throw new \Exception($e);
            exit();
        }
    }

    /**
     * Редактировать
     *
     * @param Apartment $model
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function update(Apartment $model)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $model->setAttributes($this->getAttributes(), false);
            $model->date_update = date('Y-m-d H:i:s');

            if (!$model->save(false)) {
                throw new \Exception('Apartment not create images');
            }

            if (!$this->updateMetroStations()) {
                throw new \Exception('Apartment not update metro stations');
            }

            if (!$this->createImages(false)) {
                throw new \Exception('Apartment not create images');
            }

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e);
            return false;
        }
    }

    /**
     * Отдает список айди метро станций, этого апартамента
     * @return array
     */
    public function getMetroStationsArray()
    {
        if ($this->_metroStationsArray === null) {
            if ($this->metroStations === null) {
                return null;
            }

            $this->_metroStationsArray = ArrayHelper::getColumn($this->metroStations, 'metro_id');
        }
        return $this->_metroStationsArray;
    }

    /**
     * Записыват свойство $_metroStationsArray
     * @param $values
     */
    public function setMetroStationsArray($values)
    {
        $this->_metroStationsArray = $values;
    }

    /**
     * Добавляет картинки
     *
     * @param bool $newApartment
     * @return bool
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

    protected function createImages($newApartment = true)
    {
        $translit = $this->rus2translit($this->address);

        reset($this->files);
        $first = key($this->files);
        foreach ($this->files as $key => $file) {
            $image = new Image();
            if ($fileName = $image->upload($file, true, true, $translit)) {
                $image->apartment_id = $this->apartment_id;
                $image->review = $fileName;
                $image->preview = $fileName;
                $image->sort = $key + 1;
                if ($newApartment && $first === $key) {
                    $image->default_img = 1;
                } elseif (!$newApartment && $first === $key) {
                    $default_img = Image::find()
                        ->where(['apartment_id' => $this->apartment_id, 'default_img' => 1])
                        ->one();
                    if (!$default_img) {
                        $image->default_img = 1;
                    }
                }

                if (!$image->save(false)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Редактирует метростанции
     *
     * Фактически создает и/или удаляет записи метростанций в БД этого апартамента
     *
     * @return bool
     */
    protected function updateMetroStations()
    {
        // Сравниваем текущие записи с новыми
        $oldMetroIds = [];
        if ($this->metroStations) {
            $oldMetroIds = ArrayHelper::getColumn($this->metroStations, 'metro_id');
        }

        $newMetroIds = $this->metroStationsArray;

        // На первом этапе удаляем удаленные метростанции
        $deletedIds = array_diff($oldMetroIds, $newMetroIds);
        if ($deletedIds) {
            ApartmentMetroStations::deleteAll(['metro_id' => $deletedIds, 'apartment_id' => $this->apartment_id]);

            //@TODO: Записать действия в лог
            //Yii::$app->user->action(Yii::$app->user->id, $this->module->id, 'delete-agency-apartment-metro', ['apartment_id' => $this->apartment_id, 'metro_id' => ['in', $deletedIds]]);
        }

        // Второй этап создание новых метростанций
        $createdIds = array_diff($newMetroIds, $oldMetroIds);
        foreach ($createdIds as $createdId) {
            $metroStation = new ApartmentMetroStations();
            $metroStation->apartment_id = $this->apartment_id;
            $metroStation->metro_id = $createdId;

            if (!$metroStation->save(false)) {
                return false;
            }
        }

        if ($createdIds) {
            //@TODO: Записать действия в лог
            //Yii::$app->user->action(Yii::$app->user->id, $this->module->id, 'create-agency-apartment-metro', ['apartment_id' => $this->apartment_id, 'metro_id' => ['in', $createdIds]]);
        }

        return true;
    }
}
