<?php

namespace common\modules\partners\models\frontend;

use Yii;

/**
 * @inheritdoc
 */
class Apartment extends \common\modules\partners\models\Apartment
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * WARNING! не соеденять с другими таблицами с помощью Join
     * @return \yii\db\ActiveQuery
     */
    public function getOrderedImages()
    {
        return $this->hasMany(Image::class, ['apartment_id' => 'apartment_id'])->orderBy('sort ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleImage()
    {
        return $this->hasOne(Image::class, ['apartment_id' => 'apartment_id'])->andWhere(['default_img' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlternateTitleImage()
    {
        return $this->hasOne(Image::class, ['apartment_id' => 'apartment_id'])->andWhere(['default_img' => 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetroStations()
    {
        return $this->hasMany(MetroStations::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetroStation()
    {
        return $this->hasOne(MetroStations::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdverts()
    {
        return $this->hasMany(Advert::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalenderActualRecord()
    {
        return $this->hasOne(Calendar::class, ['apartment_id' => 'apartment_id'])
            ->andWhere(['process' => 1, 'reserved' => Calendar::FREE]);
    }

    /**
     * Возвращает адрес заглавной картинки
     * Для уменьшения количества запросов к БД, рекомендуется использовать вместе с реляцией titleImage
     * @return string
     */
    public function getTitleImageSrc()
    {
        $folder = Yii::getAlias(Yii::$app->getModule('partners')->imagesPath);
        if ($this->titleImage) {
            $file = $folder . '/' . $this->titleImage->review;
            if (file_exists($file)) {
                return Yii::$app->getModule('partners')->previewImagesUrl . '/' . $this->titleImage->preview;
            }
        }
        if ($this->alternateTitleImage) {
            $file = $folder . '/' . $this->alternateTitleImage->review;
            if (file_exists($file)) {
                return Yii::$app->getModule('partners')->previewImagesUrl . '/' . $this->alternateTitleImage->preview;
            }
        }

        return Yii::$app->getModule('partners')->defaultImageSrc;
    }
}
