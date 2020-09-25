<?php

namespace common\modules\partners\models\backend;

/**
 * @inheritdoc
 * @package common\modules\partners\models\backend
 */
class Apartment extends \common\modules\partners\models\Apartment
{
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
    public function getCity()
    {
        return $this->hasOne(\common\modules\geo\models\City::class, ['city_id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleImage()
    {
        return $this->hasOne(Image::class, ['apartment_id' => 'apartment_id'])
            ->andWhere(['default_img' => 1]);
    }

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
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainDistrict()
    {
        return $this->hasOne(\common\modules\geo\models\Districts::class, ['id' => 'district1']);
    }
}
