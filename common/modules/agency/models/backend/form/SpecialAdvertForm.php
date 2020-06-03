<?php

namespace common\modules\agency\models\backend\form;

use common\modules\agency\models\SpecialAdvert;
use common\modules\agency\models\Advert;
use Yii;

/**
 * Special Advert Form
 * @package common\modules\agency\models\backend\form
 */
class SpecialAdvertForm extends \yii\base\Model
{
    public $special_id;
    public $advert_id;
    public $text;
    public $date_start;
    public $date_expire;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new SpecialAdvert())->attributeLabels(), [

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
        $model = new SpecialAdvert();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return false;
        }

        $this->special_id = $model->special_id;
        return true;
    }

    /**
     * Редактировать
     *
     * @param SpecialAdvert $model
     * @return bool
     */
    public function update(SpecialAdvert $model)
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
