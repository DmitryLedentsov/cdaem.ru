<?php

namespace common\modules\partners\models\backend;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @inheritdoc
 * @package common\modules\partners\models\backend
 */
class ApartmentForm extends \common\modules\partners\models\Apartment
{
    /**
     * @var array
     */
    public $files;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update',
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                }
            ],
            'ActionBehavior' => [
                'class' => \nepster\users\behaviors\ActionBehavior::class,
                'module' => $this->module->id,
                'actions' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create-partners-apartment',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update-partners-apartment',
                    ActiveRecord::EVENT_BEFORE_DELETE => 'delete-partners-apartment',
                ],
            ],
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        return array_merge($attributeLabels, [
            'files' => 'Выберите изображения'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'create' => self::OP_ALL,
            'update' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['user_id', 'city_name' /*'city_id', 'closest_city_id'*/, 'address', 'apartment', 'floor', 'total_rooms', 'total_area', 'visible', 'status', 'remont', 'metro_walk', 'description', 'files'],
            'update' => ['user_id', 'city_name' /*'city_id', 'closest_city_id'*/, 'address', 'apartment', 'floor', 'total_rooms', 'total_area', 'visible', 'status', 'remont', 'metro_walk', 'description', 'date_create', 'date_update', 'files']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', /*'city_name', 'city_id', 'closest_city_id',*/ 'apartment', 'floor', 'total_rooms', 'total_area', 'visible', 'status', 'remont', 'metro_walk'], 'integer'],
            ['user_id', 'exist', 'targetClass' => \common\modules\users\models\User::class, 'targetAttribute' => 'id'],
            // [['city_id', 'closest_city_id'], 'exist', 'targetClass' => \common\modules\geo\models\City::class, 'targetAttribute' => 'city_id'],
            [['city_name'], 'string'],
            [['visible', 'status'], 'in', 'range' => [0, 1, 2]],
            ['remont', 'in', 'range' => array_keys($this->remontList)],
            [['address'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['files'], 'file', 'extensions' => 'jpg, png, jpeg', 'mimeTypes' => 'image/jpeg, image/png, image/JPG, ', 'maxFiles' => 10],
            // required on %scenarios%
            [['user_id', /*'city_id'*/ 'city_name', 'region_name', 'address', 'floor', 'total_rooms', 'total_area',
                'visible', 'status', 'remont', 'metro_walk',
            ], 'required', 'on' => 'create'],
            [['user_id', /*'city_id',*/ 'city_name', 'region_name', 'address',
                'floor', 'total_rooms', 'total_area', 'visible', 'status', 'remont', 'metro_walk',
            ], 'required', 'on' => 'update'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $adverts = Advert::find()->where(['apartment_id' => $this->apartment_id])->all();
        foreach ($adverts as $advert) {
            $advert->old_position = 0;
            $advert->position = 1;
            //$advert->real_position = 1;
            $advert->save(false);
        }

        reset($this->files);
        $this->files = array_filter($this->files);
        $first = key($this->files);

        foreach ($this->files as $key => $file) {
            $image = new Image();
            if ($fileName = $image->upload($file, true, true)) {
                $image->apartment_id = $this->apartment_id;
                $image->review = $fileName;
                $image->preview = $fileName;
                $image->sort = $key + 1;
                if ($insert && $first === $key) {
                    $image->default_img = 1;
                } elseif (!$insert && $first === $key) {
                    $default_img = Image::find()
                        ->where(['apartment_id' => $this->apartment_id, 'default_img' => 1])
                        ->one();
                    if (!$default_img) {
                        $image->default_img = 1;
                    }
                }
                $image->save(false);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
