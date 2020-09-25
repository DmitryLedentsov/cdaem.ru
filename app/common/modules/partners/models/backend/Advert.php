<?php

namespace common\modules\partners\models\backend;

use Yii;

/**
 * @inheritdoc
 * @package common\modules\partners\models\backend
 */
class Advert extends \common\modules\partners\models\Advert
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['apartment_id', 'rent_type', 'price', 'currency'],
            'update' => ['apartment_id', 'rent_type', 'price', 'currency'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_id', 'rent_type', 'currency',], 'integer'],
            ['apartment_id', 'exist', 'targetClass' => \common\modules\partners\models\Apartment::class, 'targetAttribute' => 'apartment_id'],
            ['rent_type', 'exist', 'targetClass' => \common\modules\realty\models\RentType::class, 'targetAttribute' => 'rent_type_id'],
            ['currency', 'in', 'range' => array_keys($this->currencyList)],
            [['price'], 'number'],
            [['rent_type'], 'unique', 'targetClass' => \common\modules\partners\models\Advert::class,
                'targetAttribute' => ['apartment_id', 'rent_type'],
                'message' => 'Для этих апартаментов уже есть такой тип аренды',
                'when' => function ($model) {
                    return $model->isNewRecord;
                }
            ],
            // required on
            [['rent_type', 'price', 'currency',], 'required', 'on' => 'create'],
            [['rent_type', 'price', 'currency',], 'required', 'on' => 'update'],
        ];
    }

    /**
     * Текстовое представление обозначения на главной странице или нет
     * @return string
     */
    public function getMainPageText()
    {
        return Yii::$app->BasisFormat->helper('Status')->getItem($this->mainPageArray, $this->main_page);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentType()
    {
        return $this->hasOne(\common\modules\realty\models\RentType::class, ['rent_type_id' => 'rent_type']);
    }
}
