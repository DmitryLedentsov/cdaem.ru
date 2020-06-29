<?php

namespace common\modules\users\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * @inheritdoc
 */
class LegalPerson extends \nepster\users\models\LegalPerson
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'legal_address', 'physical_address', 'formattedRegisterDate', 'INN', 'PPC', 'account', 'bank', 'KS', 'BIK', 'BIN', 'director', 'description'],
            'update' => ['name', 'legal_address', 'physical_address', 'formattedRegisterDate', 'INN', 'PPC', 'account', 'bank', 'KS', 'BIK', 'BIN', 'director', 'description'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'create' => self::OP_ALL,
            'update' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название фирмы',
            'legal_address' => 'Юридический адрес',
            'physical_address' => 'Физический адрес',
            'register_date' => 'Дата регистрации',
            'formattedRegisterDate' => 'Дата регистрации',
            'INN' => 'ИНН',
            'PPC' => 'КПП',
            'account' => 'Расчетный счет',
            'bank' => 'Наименование банка',
            'KS' => 'К/с',
            'BIK' => 'Бик',
            'BIN' => 'ОГРН',
            'director' => 'Ген. директор',
            'description' => 'Дополнительная информация',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'trim'],

            ['legal_address', 'required'],
            ['legal_address', 'trim'],

            ['physical_address', 'required'],
            ['physical_address', 'trim'],

            ['formattedRegisterDate', 'required'],
            ['formattedRegisterDate', 'date', 'format' => 'php:d.m.Y'],
            ['formattedRegisterDate', 'trim'],

            ['INN', 'required'],
            ['INN', 'trim'],

            ['PPC', 'required'],
            ['PPC', 'trim'],

            ['account', 'required'],
            ['account', 'trim'],

            ['bank', 'required'],
            ['bank', 'trim'],

            ['KS', 'required'],
            ['KS', 'trim'],

            ['BIK', 'required'],
            ['BIK', 'trim'],

            ['BIN', 'required'],
            ['BIN', 'trim'],

            ['director', 'required'],
            ['director', 'trim'],

            ['description', 'string'],
            ['description', 'trim'],

            [['name', 'legal_address', 'physical_address', 'formattedRegisterDate', 'INN', 'PPC', 'account', 'bank',
                'KS', 'BIK', 'BIN', 'director', 'description'], 'string', 'max' => 100],
        ];
    }

    public function getFormattedRegisterDate()
    {
        return date('d.m.Y', strtotime($this->register_date));
    }

    public function setFormattedRegisterDate($value)
    {
        $this->register_date = date('Y-m-d', strtotime($value));
    }

    /**
     * @inheritdoc
     */
    /*public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            $validator = new \yii\validators\DateValidator();
            $validator->format = 'php:d.m.Y';


            if (!$validator->validate($this->register_date)) {
                $this->addError('register_date', 'Некорректный формат');
            }

            return true;
        }

        return false;
    }*/

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->user_id) {
                $this->user_id = Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }
}
