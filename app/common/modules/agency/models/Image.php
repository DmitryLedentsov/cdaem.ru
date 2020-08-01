<?php

namespace common\modules\agency\models;

use common\modules\agency\traits\ModuleTrait;
use Yii;

/**
 * Изображения к апартаментам
 * @package common\modules\agency\models
 */
class Image extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_apartment_images}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'image_id' => '№',
            'apartment_id' => 'Apartment ID',
            'default_img' => 'Заглавное изображение',
            'preview' => 'Лицевое изображение',
            'review' => 'Изображение',
            'title' => 'Title',
            'alt' => 'Alt',
        ];
    }

    /**
     * Вовзращает Alt этой картинки для просмотра отдельного объявления
     * в случае если в БД не назначено, то возвращает специально генерируемый Alt
     * @return string
     */
    public function getFrontAlt()
    {
        if ($this->alt) {
            return $this->alt;
        }

        $alt = $this->apartment->city->name;
        $alt .= ", ул. " . $this->apartment->address;
        $alt .= $this->apartment->apartment ? ', кв. ' . $this->apartment->apartment : '';

        return $alt;
    }

    /**
     * Вовзращает Title этой картинки для просмотра отдельного объявления
     * в случае если в БД не назначено, то возвращает специально генерируемый Title
     * @return string
     */
    public function getFrontTitle()
    {
        if ($this->title) {
            return $this->title;
        }

        $title = "Фото " . $this->apartment->city->name;
        $title .= ", ул. " . $this->apartment->address;
        $title .= $this->apartment->apartment ? ', кв. ' . $this->apartment->apartment : '';

        return $title;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * Возвращает SRC маленькой картинки
     * @return string
     */
    public function getPreviewSrc()
    {
        $file = Yii::getAlias(Yii::$app->getModule('agency')->imagesPath) . '/' . $this->review;
        if (file_exists($file)) {
            return Yii::$app->params['siteDomain'] . Yii::$app->getModule('agency')->previewImagesUrl . '/' . $this->preview;
        } else {
            return Yii::$app->params['siteDomain'] . Yii::$app->getModule('agency')->defaultImageSrc;
        }
    }

    /**
     * Возвращает SRC большой картинки
     * @return string
     */
    public function getReviewSrc()
    {
        $file = Yii::getAlias(Yii::$app->getModule('agency')->imagesPath) . '/' . $this->review;
        if (file_exists($file)) {
            return Yii::$app->params['siteDomain'] . Yii::$app->getModule('agency')->imagesUrl . '/' . $this->review;
        } else {
            return Yii::$app->params['siteDomain'] . Yii::$app->getModule('partners')->defaultImageSrc;
        }
    }

    /**
     * Удаляет запись соответствующую этой($this) ActiveRecord из БД вместе с файлами из сервера
     * и в случае если удаленная запись была заглавной, назначается новая заглавная картинка, если существуют еще.
     * Пожалуйста обратитесь к [[ActiveRecord::delete()]] чтобы узнать выбрасываемы исключения
     * @return integer|false количество удаленных записей или false в случае неудачи удаления
     */
    public function deleteWithFiles()
    {
        self::deleteFile($this->review, $this->preview);

        if ($this->default_img == 1) {
            $newTitleImage = self::find()
                ->where(['apartment_id' => $this->apartment_id, 'default_img' => 0])
                ->one();
            if ($newTitleImage) {
                $newTitleImage->default_img = 1;
                $newTitleImage->save();
            }
        }

        return $this->delete();
    }

    /**
     * Удаляет записи из БД вместе с файлами из сервера
     * @param string|array $condition
     * Пожалуйста обратитесь к [[ActiveRecord::deleteAll()]] чтобы узнать как назначать параметры.
     * @param array $params
     * @return integer количество удаленных записей
     */
    public static function deleteAllWithFiles($condition = '', $params = [])
    {
        $apartmentImages = self::find()->where($condition, $params)->all();
        $count = 0;
        $apartments = [];
        foreach ($apartmentImages as $image) {
            self::deleteFile($image->review, $image->preview);
            // Запоминаем с каких апартаментов удалили заглавные картинки
            if ($image->default_img == 1) $apartments[$image->apartment_id] = true;
            $count += $image->delete();
        }

        // Назначаем заглавные апартаментам
        $apartmentIds = array_keys($apartments);
        foreach ($apartmentIds as $apartment_id) {
            $newTitleImage = self::find()
                ->where(['apartment_id' => $apartment_id, 'default_img' => 0])
                ->one();
            if ($newTitleImage) {
                $newTitleImage->default_img = 1;
                $newTitleImage->save();
            }
        }

        return $count;
    }

    /**
     * @param string $review имья большого файла картинки
     * @param string $preview имья маленького файла картинки
     * Удаляет Файл изображения
     */
    protected static function deleteFile($review, $preview)
    {
        @unlink(Yii::getAlias(Yii::$app->getModule('agency')->imagesPath) . '/' . $review);
        @unlink(Yii::getAlias(Yii::$app->getModule('agency')->previewImagesPath) . '/' . $preview);
    }

    /**
     * Загрузка изображения на сервер
     * Функция загружает изображение во временную папку
     * Сохраняет, ресайзит и накладывает водных знак, а так-же
     * параллельно может сохранять превью изображения используя настройки модуля.
     * @param $image
     * @param bool $watermark
     * @param bool $preview
     * @return bool
     */
    public function upload($image, $watermark = true, $preview = true, $filenamed = "default")
    {
        // Инициализируем расширение image
        $reviewImage = Yii::$app->image->load($image->tempName);
        $previewImage = Yii::$app->image->load($image->tempName);

        // Имя изображения
        $fileName = 'photo_' . $filenamed . '_' . uniqid('4', false) . '.' . $image->extension;

        // Ширина кропа
        $imageResizeWidth = $this->module->imageResizeWidth;

        // Ресайз
        if ($reviewImage->width > $imageResizeWidth || $reviewImage->height > $imageResizeWidth) {
            $reviewImage->resize($imageResizeWidth, $imageResizeWidth);
        }

        // Водный знак
        if ($watermark) {
            $watermarkLoad = Yii::$app->image->load(Yii::getAlias($this->module->imagesWatermarkPath));
            $reviewImage->watermark($watermarkLoad);
        }

        // Сохранить
        $imagesPath = $this->module->imagesPath;
        if (!$reviewImage->save(Yii::getAlias($imagesPath) . DIRECTORY_SEPARATOR . $fileName, 80)) {
            return false;
        }

        // Превью
        if ($preview) {
            $imageResizeWidth = $this->module->previewImageResizeWidth;
            $previewImage->resize($imageResizeWidth, $imageResizeWidth);

            // Водный знак
            if ($watermark) {
                $watermarkLoad = Yii::$app->image->load(Yii::getAlias($this->module->imagesWatermarkPath));
                $previewImage->watermark($watermarkLoad);
            }

            $previewImage->save(Yii::getAlias($this->module->previewImagesPath) . DIRECTORY_SEPARATOR . $fileName, 80);
        }

        return $fileName;
    }

}
