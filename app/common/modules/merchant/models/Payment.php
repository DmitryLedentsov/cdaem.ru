<?php

namespace common\modules\merchant\models;

use yii\db\ActiveRecord;
use common\modules\merchant\traits\ModuleTrait;

/**
 * Class Payments
 * @package common\modules\merchant\models
 */
class Payment extends ActiveRecord
{
    use ModuleTrait;

    /**
     * Тип Денежного Оборота
     * - Пополение
     * - Начисление
     * - Расходы
     */
    const DEPOSIT = 'DEPOSIT';

    const BILLING = 'BILLING';

    const COSTS = 'COSTS';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
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
    public static function tableName()
    {
        return '{{%merchant_payments}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'date' => 'Дата',
            'funds' => 'Средства',
            'funds_was' => 'Было на счету',
            'type' => 'Тип',
            'system' => 'Система',
            'module' => 'Модуль',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }

    /**
     * Массив Доступных Данных
     * @return array
     */
    public static function getTypeArray()
    {
        return [
            self::DEPOSIT => [
                'label' => 'Пополнение',
                'style' => 'color: green',
            ],
            self::BILLING => [
                'label' => 'Начисление',
                'style' => 'color: green',
            ],
            self::COSTS => [
                'label' => 'Трата',
                'style' => 'color: red',
            ]
        ];
    }

    /**
     * Создать новую запись
     * @param $module
     * @param $type
     * @param $system
     * @param $userId
     * @param $fundsWas
     * @param $funds
     * @return integer
     */
    public static function newPayment($module, $type, $system, $userId, $fundsWas, $funds)
    {
        $payment = new self;
        $payment->module = $module;
        $payment->type = $type;
        $payment->system = $system;
        $payment->user_id = $userId;
        $payment->funds_was = $fundsWas;
        $payment->funds = $funds;
        $payment->save(false);

        return $payment->payment_id;
    }
}
