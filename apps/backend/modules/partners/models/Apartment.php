<?php

namespace backend\modules\partners\models;

use Yii;

/**
 * @inheritdoc
 */
class Apartment extends \common\modules\partners\models\Apartment
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdverts()
    {
        return $this->hasMany(Advert::className(), ['apartment_id' => 'apartment_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(\common\modules\geo\models\City::className(), ['city_id' => 'city_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleImage()
    {
        return $this->hasOne(Image::className(), ['apartment_id' => 'apartment_id'])
            ->andWhere(['default_img' => 1]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['apartment_id' => 'apartment_id']);
    }

    /**
     * WARNING! не соеденять с другими таблицами с помощью Join
     * @return \yii\db\ActiveQuery
     */
    public function getOrderedImages()
    {
        return $this->hasMany(Image::className(), ['apartment_id' => 'apartment_id'])->orderBy('sort ASC');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainDistrict()
    {
        return $this->hasOne(\common\modules\geo\models\Districts::className(), ['id' => 'district1']);
    }
}
