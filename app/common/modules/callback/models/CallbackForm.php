<?php

namespace common\modules\callback\models;

use common\modules\callback\models\Callback;
use yii\base\Model;
use yii;

/**
 * Форма "Обратный звонок"
 * @package common\modules\callback\models\backend
 */
class CallbackForm extends Model
{
    public $phone;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Callback())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->phone  = str_replace(['(', ')', '+', ' ', '-'], '', $this->phone);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'Callback';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['phone'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],
        ];
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Callback();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return false;
        }

        return true;
    }
}
