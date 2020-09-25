<?php

namespace common\modules\agency\models\backend\form;

use common\modules\agency\models\Advert;
use common\modules\agency\models\Advertisement;

/**
 * Advertisement Form
 * @package common\modules\agency\models\backend\form
 */
class AdvertisementForm extends \yii\base\Model
{
    public $advertisement_id;

    public $advert_id;

    public $text;

    public $date_start;

    public $date_expire;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Advertisement())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['advert_id', 'text', 'date_start', 'date_expire'],
            'update' => ['advert_id', 'text', 'date_start', 'date_expire'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advert_id', 'text', 'date_start', 'date_expire'], 'required'],
            [['advert_id', 'text', 'date_start', 'date_expire'], 'required'],

            ['advert_id', 'exist', 'targetClass' => 'common\modules\agency\models\Advert', 'targetAttribute' => 'advert_id'],

            [['date_start', 'date_expire'], 'date', 'format' => 'php:Y-m-d H:i:s'],

            ['date_expire', 'compare', 'compareValue' => (new \DateTime('NOW'))->format('Y-m-d H:i:s'), 'operator' => '>'],

            ['date_start', 'compare', 'compareAttribute' => 'date_expire', 'operator' => '!='],

            ['text', 'string', 'max' => 255],
        ];
    }

    /**
     * Создать
     *
     * @return bool
     */
    public function create()
    {
        $model = new Advertisement();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return false;
        }

        $this->advertisement_id = $model->advertisement_id;

        return true;
    }

    /**
     * Редактировать
     *
     * @param Advertisement $model
     * @return bool
     */
    public function update(Advertisement $model)
    {
        $model->setAttributes($this->getAttributes(), false);

        return $model->save(false);
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getAdvert()
    {
        return Advert::find()
            ->joinWith(['rentType'])
            ->where(['advert_id' => $this->advert_id])
            ->one();
    }
}
