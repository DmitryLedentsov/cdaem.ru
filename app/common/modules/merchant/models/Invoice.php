<?php

namespace common\modules\merchant\models;

use yii\db\ActiveRecord;
use common\modules\merchant\traits\ModuleTrait;

/**
 * Модель счет-фактура
 */
class Invoice extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%merchant_invoice}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date_create',
                ],
                "value" => function () {
                    return date('Y-m-d H:i:s');
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'date_create' => 'Дата создания',
            'date_payment' => 'Дата оплаты',
            'funds' => 'Средства',
            'system' => 'Система',
            'paid' => 'Оплата',
        ];
    }

    /**
     * Поиск активного счета по id
     * @param $id
     * @return array|null|ActiveRecord
     */
    public static function findNoPaidById($id)
    {
        return self::find()
            ->where('invoice_id = :invoice_id', ['invoice_id' => $id])
            ->andWhere('paid = 0')
            ->one();
    }

    /**
     * Типы
     * - roobokassa
     */
    const ROBOKASSA = 'roobokassa';

    /**
     * @return array Массив доступных данных
     */
    public static function getTypesArray()
    {
        return [
            self::ROBOKASSA => [
                'label' => 'ROBOKASSA',
                'style' => '',
            ],
        ];
    }
}
