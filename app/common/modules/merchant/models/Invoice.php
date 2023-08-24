<?php

namespace common\modules\merchant\models;

use yii\db\ActiveRecord;
use common\modules\merchant\traits\ModuleTrait;

/**
 * Модель счет-фактура
 *
 * @property int $invoice_id
 * @property int $process_id
 * @property string $hash
 * @property int $user_id
 * @property string $system
 * @property float $funds
 *
 */
class Invoice extends ActiveRecord
{
    use ModuleTrait;

    /**
     * Типы
     * - robokassa
     */
    public const ROBOKASSA = 'robokassa';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%merchant_invoice}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
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
    public function attributeLabels(): array
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
     * @return array Массив доступных данных
     */
    public static function getTypesArray(): array
    {
        return [
            self::ROBOKASSA => [
                'label' => 'ROBOKASSA',
                'style' => '',
            ],
        ];
    }
}
