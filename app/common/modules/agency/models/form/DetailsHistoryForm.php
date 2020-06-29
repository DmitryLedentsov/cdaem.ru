<?php

namespace common\modules\agency\models\form;

use common\modules\agency\models\DetailsHistory;
use yii\helpers\Html;
use Yii;

/**
 * Details History Form
 * @package common\modules\agency\models\form
 */
class DetailsHistoryForm extends \yii\base\Model
{
    public $advert_id;
    public $type;
    public $payment;
    public $phone;
    public $email;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new DetailsHistory)->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['phone', 'email', 'type', 'payment', 'advert_id'],
            'bank' => ['phone', 'email', 'type', 'payment', 'advert_id'],
            'system' => ['phone', 'email', 'type', 'advert_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['advert_id', 'required'],
            ['advert_id', 'exist', 'targetClass' => '\common\modules\agency\models\Advert', 'targetAttribute' => 'advert_id'],

            ['phone', 'required'],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],

            ['email', 'required'],
            ['email', 'email'],

            ['payment', 'required'],
            ['payment', 'in', 'range' => array_keys(DetailsHistory::getPaymentArray())],

            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(DetailsHistory::getTypeArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->phone = str_replace(['(', ')', '+', ' ', '-'], '', $this->phone);

        $this->scenario = 'system';
        if ($this->type == DetailsHistory::TYPE_SBERBANK || $this->type == DetailsHistory::TYPE_ALFABANK) {
            $this->scenario = 'bank';
        }


        if (empty($this->payment)) {
            $this->payment = 2;
        }

        return true;
    }

    /**
     * @return array
     */
    public static function getPaymentArray()
    {
        return DetailsHistory::getPaymentArray();
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new DetailsHistory;
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');
        return $model->save(false);
    }

}
