<?php

namespace common\modules\agency\models;

use common\modules\agency\traits\ModuleTrait;
use Yii;

/**
 * История отправки реквизитов
 * @package common\modules\agency\models
 */
class DetailsHistory extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agency_details_history}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'phone' => 'Телефон',
            'email' => 'EMAIL',
            'type' => 'Тип',
            'advert_id' => 'ID объявления',
            'payment' => 'Реквизиты',
            'data' => 'Данные',
            'processed' => 'Обработана',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }

    /**
     * Реквизиты
     * @return array
     */
    public static function getPaymentArray()
    {
        return [
            1 => 'Номер карты',
            2 => 'Расчетный счет',
        ];
    }

    /**
     * Типы платежного шлюза
     * - Сбербанк
     * - Альфа - Банк
     * - Яндекс.Деньги
     * - Qiwi
     * - Телефон
     */
    const TYPE_SBERBANK = 'sberbank';
    const TYPE_ALFABANK = 'alfabank';
    const TYPE_YAMONEY = 'yamoney';
    const TYPE_QIWI = 'qiwi';
    const TYPE_PHONE = 'phone';
    const TYPE_LEGAL = 'legal';

    /**
     * Тип платежного шлюза
     * @return array
     */
    public static function getTypeArray()
    {
        return [
            self::TYPE_SBERBANK => 'Сбербанк',
            self::TYPE_ALFABANK => 'Альфа - Банк',
            self::TYPE_YAMONEY => 'Яндекс.Деньги',
            self::TYPE_QIWI => 'Qiwi',
            self::TYPE_PHONE => 'Телефон',
            self::TYPE_LEGAL => 'Юридическое лицо',
        ];
    }
}