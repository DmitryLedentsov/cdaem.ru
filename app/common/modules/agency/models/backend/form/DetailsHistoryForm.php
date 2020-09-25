<?php

namespace common\modules\agency\models\backend\form;

use common\modules\agency\models\DetailsHistory;

/**
 * Details History Form
 * @package common\modules\agency\models\backend\form
 */
class DetailsHistoryForm extends \yii\base\Model
{
    public $id;

    public $phone;

    public $email;

    public $processed;

    public $type;

    public $payment;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['type', 'payment', 'phone', 'email', 'processed'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phone', 'number'],
            ['email', 'email'],
            ['processed', 'boolean'],
            [['phone', 'email'], 'required', 'on' => 'update']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new DetailsHistory())->attributeLabels(), [

        ]);
    }

    /**
     * Редактировать
     *
     * @param DetailsHistory $model
     * @return bool
     */
    public function update(DetailsHistory $model)
    {
        $model->setAttributes($this->getAttributes(), false);

        return $model->save(false);
    }
}
