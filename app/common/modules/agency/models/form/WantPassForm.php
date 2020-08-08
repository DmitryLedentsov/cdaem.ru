<?php

namespace common\modules\agency\models\form;

use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\UploadedFile;
use common\modules\agency\models\WantPass;
use common\modules\agency\traits\ModuleTrait;

/**
 * Хочу сдать квартиру
 * @package frontend\modules\agency\models\form
 */
class WantPassForm extends Model
{
    use ModuleTrait;

    public $name;

    public $email;

    public $rent_types_array;

    public $address;

    public $rooms;

    public $phone;

    public $description;

    public $metro_array;

    public $files;

    //public $verifyCode;
    protected $images;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new WantPass)->attributeLabels(), [
            //'verifyCode' => 'Защитный код',
            'files' => 'Добавить фото',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'partnership' => ['name', 'email', 'address', 'rooms', 'phone', 'description', 'metro_array', 'files'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rent_types_array', 'address', 'rooms', 'phone', 'metro_array'], 'required'],

            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 10],

            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],
            ['rent_types_array', 'default', 'value' => []],
            ['rent_types_array', 'each', 'rule' => ['in', 'range' => array_keys($this->getRentTypesList())]],
            ['metro_array', 'default', 'value' => []],
            ['metro_array', 'each', 'rule' => ['in', 'range' => array_keys($this->getMetroStations())]],
            [['rooms'], 'integer'],
            ['rooms', 'in', 'range' => array_keys($this->getRoomsList())],
            [['description'], 'string'],
            [['name', 'address'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 200],
            ['email', 'email'],

            // Защитный код

        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->phone = str_replace(['(', ')', '+', ' ', '-'], '', $this->phone);
        $this->files = UploadedFile::getInstances($this, 'files');

        return true;
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        if ($this->uploadImages()) {
            $model = new WantPass();
            $model->setAttributes($this->getAttributes(), false);
            $model->metro = Json::encode($this->metro_array);

            if ($this->scenario !== 'partnership') {
                $model->rent_types = Json::encode($this->rent_types_array);
            }

            $model->images = $this->images;
            $model->date_create = date('Y-m-d H:i:s');

            return $model->save(false);
        }

        return false;
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getRentTypesList()
    {
        return WantPass::getRentTypesList();
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getMetroStations()
    {
        return WantPass::getMetroStations();
    }

    /**
     * Список доступных вариантов количества комнат
     * @return array
     */
    public static function getRoomsList()
    {
        return WantPass::getRoomsList();
    }

    /**
     * Загрузка изображений
     * @return bool
     */
    private function uploadImages()
    {
        if ($this->files) {
            $images = [];

            foreach ($this->files as $key => $file) {
                $tmpPath = Yii::getAlias($this->module->imagesTmpPath);
                $tmpfileName = uniqid('', true) . '.' . $file->extension;

                $fileTmpPath = $tmpPath . DIRECTORY_SEPARATOR . $tmpfileName;

                // Сохранить изображение во временную директорию
                if (!$file->saveAs($fileTmpPath)) {
                    continue;
                }

                // Инициализируем расширение image
                $imageTmpLoad = $imageLoad = Yii::$app->image->load($fileTmpPath);

                // Имя изображения
                $fileName = uniqid('', true) . '.' . $file->extension;

                // Ширина кропа
                $imageResizeWidth = $this->module->imageResizeWidth;

                // Ресайз
                if ($imageLoad->width > $imageResizeWidth || $imageLoad->height > $imageResizeWidth) {
                    $imageLoad->resize($imageResizeWidth, $imageResizeWidth);
                }

                // Сохранить
                $imagesPath = $this->module->imagesWantPassPath;
                if (!$imageLoad->save(Yii::getAlias($imagesPath) . DIRECTORY_SEPARATOR . $fileName, 80)) {
                    continue;
                }

                @unlink($fileTmpPath);

                $images[] = $fileName;
            }

            $this->images = Json::encode($images);
        }

        return true;
    }
}
