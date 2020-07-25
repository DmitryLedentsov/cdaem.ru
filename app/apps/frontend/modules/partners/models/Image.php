<?php

namespace frontend\modules\partners\models;

use Yii;

/**
 * @inheritdoc
 */
class Image extends \common\modules\partners\models\Image
{
    /**
     * @var string
     */
    public $defaultImage;

    /**
     * @var array
     */
    public $files;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['files'] = 'Добавить фото';
        $labels['defaultImage'] = 'Заглавное изображение';
        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['apartment_id' => 'apartment_id']);
    }
}
