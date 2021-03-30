<?php

namespace common\modules\users\models;

use Yii;
use yii\web\UploadedFile;

/**
 * @inheritdoc
 */
class Profile extends \nepster\users\models\Profile
{
    /**
     * Фотография
     * @var mixed
     */
    public $image;

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // $this->birthday = '0000-00-00';
        $this->advertising = true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'image' => 'Загрузить фотографию',
            'avatar_url' => 'Фотография',
            'about_me' => 'О себе',
            'birthday' => 'Дата рождения',
            'second_name' => 'Отчество',
            'user_type' => 'Тип аккаунта',
            'user_partner' => 'Стать партнером',
            'user_partner_rating' => 'Партнерский рейтинг',
            'user_partner_verify' => 'Верификация',
            'advertising' => 'Согласен получать рекламу',
            'phone2' => 'Второй телефон',
            'phones' => 'Список телефонов',
            'email' => 'Дополнительный EMAIL',
            'skype' => 'Skype',
            'ok' => 'Одноклассники',
            'vk' => 'Вконтакте',
            'fb' => 'Facebook',
            'twitter' => 'Twitter',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            //'create' => ['name', 'surname', 'second_name', 'whau', 'user_type', /*'user_partner',*/ 'advertising', /*'birthday', 'day', 'month', 'year'*/],
            'create' => ['name', 'user_type', 'advertising'],
            'update' => ['name', 'surname', 'second_name', 'image', 'about_me', 'user_type', 'phone2', 'phones', 'email', 'skype', 'ok', 'vk', 'phones', 'fb', 'twitter', 'legal_person'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // User type
            ['user_type', 'required'],
            ['user_type', 'in', 'range' => array_keys($this->getUserTypeArray())],

            // User legal person
            ['legal_person', 'required'],
            ['legal_person', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],

            // User partner
            // ['user_partner', 'required'],
            // ['user_partner', 'in', 'range' => array_keys($this->getUserPartnerArray())],

            [['name', 'surname', 'second_name'], 'trim'],

            // Name
            ['name', 'required'],
            ['name', 'match', 'pattern' => '/^[a-zа-яё]+$/iu'],

            // Surname
            ['surname', 'required'],
            ['surname', 'match', 'pattern' => '/^[a-zа-яё]+(-[a-zа-яё]+)?$/iu'],

            // Second name
            ['second_name', 'match', 'pattern' => '/^[a-zа-яё]+(-[a-zа-яё]+)?$/iu'],

            // О себе
            [['about_me', 'email', 'skype', 'ok', 'vk', 'phones', 'fb', 'twitter'], 'string'],

            // Whau
            ['whau', 'string', 'min' => 1, 'max' => 200],

            // Рекламная рассылка
            ['advertising', 'boolean'],

            // Контакты
            [['phone2', 'email', 'skype', 'ok', 'vk', 'phones', 'fb', 'twitter'], 'string'],

            // Фотография
            [
                'image',
                'image',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, png',
                'mimeTypes' => 'image/jpeg, image/png',
                'maxFiles' => 1,
                'minHeight' => $this->module->avatarResizeWidth,
                'minWidth' => $this->module->avatarResizeWidth,
                'maxSize' => $this->module->maxSize
            ],

            /*
            // Birthday
            ['birthday', 'filter', 'filter' => function ($value) {
                $this->birthday[0] = \nepster\birthday\Birthday::idate($value);
            }],

            /*['day', 'required'],
            ['month', 'required'],
            ['year', 'required'],
            */
            /*
            ['birthday', 'required'],
            ['birthday', 'date', 'format' => 'php:Y-m-d'],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            /*
              print_r($this->day);
              print_r($this->month);
              print_r($this->year);
              die();*/

            // $this->birthday

            if ($this->image) {
                $this->image = UploadedFile::getInstance($this, 'image');
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
            if (is_object($this->image) && $avatarUrl = $this->uploadAvatar()) {
                $this->avatar_url = $avatarUrl;
            }

            if ($this->user_type == self::WANT_RENT) {
                $this->user_partner = self::NO_PARTNER;
            }

            return true;
        }

        return false;
    }

    /**
     * Типы личного кабинета
     * - Хочу снять
     * - Посредник
     * - Собственник
     * - Агенство
     */
    const WANT_RENT = 1;

    const MEDIATOR = 2;

    const OWNER = 3;

    const AGENCY = 4;

    /**
     * @return array
     */
    public static function getUserTypeArray()
    {
        return [
            self::AGENCY => [
                'label' => 'Агентство',
            ],
            self::OWNER => [
                'label' => 'Собственник',
            ],
            self::MEDIATOR => [
                'label' => 'Посредник',
            ],
            self::WANT_RENT => [
                'label' => 'Хочу снять',
            ],
        ];
    }

    /**
     * Партнер
     * - Хочу снять
     * - Посредник
     */
    const NO_PARTNER = 0;

    const PARTNER = 1;

    /**
     * @return array
     */
    public static function getUserPartnerArray()
    {
        return [
            self::NO_PARTNER => [
                'label' => 'Нет',
            ],
            self::PARTNER => [
                'label' => 'Да',
            ],
        ];
    }

    /**
     * Загрузить изображение
     * @return bool|string
     */
    private function uploadAvatar()
    {
        $tmpPath = Yii::getAlias($this->module->avatarsTempPath);
        $tmpfileName = uniqid('', true) . '.' . $this->image->extension;

        $fileTmpPath = $tmpPath . DIRECTORY_SEPARATOR . $tmpfileName;

        // Сохранить изображение во временную директорию
        if (!$this->image->saveAs($fileTmpPath)) {
            return false;
        }

        // Инициализация изображения
        $imageLoad = Yii::$app->image->load($fileTmpPath);

        // Имя изображения
        $fileName = $this->user_id . '.' . $this->image->extension;

        // Ширина кропа
        $imageResizeWidth = $this->module->avatarResizeWidth;

        // Ресайз
        if ($imageLoad->width > $imageResizeWidth || $imageLoad->height > $imageResizeWidth) {
            $imageLoad->resize($imageResizeWidth, $imageResizeWidth);
        }

        // Сохранить
        $imagesPath = $this->module->avatarPath;
        $result = $imageLoad->save(Yii::getAlias($imagesPath) . DIRECTORY_SEPARATOR . $fileName, 80);

        @unlink($fileTmpPath);

        if ($result) {
            return $fileName;
        }

        return false;
    }
}
