<?php

namespace frontend\modules\partners\models\form;

use Yii;
use frontend\modules\partners\models\AdvertisementSlider;

/**
 * @inheritdoc
 */
class AdvertisementSliderForm extends \frontend\modules\partners\models\AdvertisementSlider
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'date_create',
                ],
                "value" => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'BlameableBehavior' => [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            // Сдать
            'user-rent' => ['type', 'label', 'more_info', 'advert_id'],
            // Есть клиент
            'user-client' => ['type', 'label', 'more_info'],
            // Хочу снять
            'user-reserv' => ['type', 'label', 'more_info'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'user-rent' => self::OP_ALL,
            'user-client' => self::OP_ALL,
            'user-reserv' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['advert_id', 'required'],
            ['advert_id', 'exist', 'targetClass' => '\frontend\modules\partners\models\Advert', 'targetAttribute' => 'advert_id'],
            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(self::getTypeArray())],
            ['label', 'required'],
            ['label', 'in', 'range' => array_keys(self::getLabelArray())],
            ['more_info', 'string', 'max' => 100],
            ['more_info', 'match', 'pattern' => '%^(?!(?:.*?\d){7})(.+)$%u'],];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->payment = 0;
            $this->visible = 1;

            return true;
        }

        return false;
    }

    /**
     * Определить сценарий для формы
     */
    public function defineScenario()
    {
        $data = Yii::$app->request->post($this->formName());

        $type = isset($data['type']) ? $data['type'] : null;

        $this->scenario = 'user-rent';

        if ($type) {
            if ($type == $this::TYPE_CLIENT) {
                $this->scenario = 'user-client';
            }

            if ($type == $this::TYPE_RESERV) {
                $this->scenario = 'user-reserv';
            }
        }
    }
}
