<?php

namespace common\modules\callback\models;

/**
 * Callback
 * @package common\modules\callback\models
 */
class Callback extends \yii\db\ActiveRecord
{
    /**
     * Статус обработана ли заявка
     * 1 - Обработанная
     * 0 - Необработанная
     */
    const PROCESSED = 1;

    const UNPROCESSED = 0;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'callback_id' => 'ID',
            'phone' => 'Телефон',
            'active' => 'Перезвонили',
            'date_create' => 'Дата создания',
            'date_processed' => 'Дата обработки',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%callback}}';
    }
}
