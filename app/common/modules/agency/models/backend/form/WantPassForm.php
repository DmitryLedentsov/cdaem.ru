<?php

namespace common\modules\agency\models\backend\form;

use yii\helpers\Json;
use common\modules\agency\models\WantPass;

/**
 * Want Pass
 * @package common\modules\agency\models\backend\form
 */
class WantPassForm extends \yii\base\Model
{
    public $apartment_want_pass_id;

    public $name;

    public $phone;

    public $phone2;

    public $email;

    public $rooms;

    public $description;

    public $address;

    public $rent_types;

    public $metro;

    public $status;

    public $date_create;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new WantPass())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['name', 'phone', 'phone2', 'email', 'rooms', 'description', 'address', 'rent_types_array', 'metro_array', 'status'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rent_types_array', 'each', 'rule' => ['in', 'range' => array_keys(WantPass::getRentTypesList())]],
            ['metro_array', 'each', 'rule' => ['in', 'range' => array_keys(WantPass::getMetroStations())]],
            ['rooms', 'in', 'range' => array_keys(WantPass::getRoomsList())],
            [['description', 'images'], 'string'],
            [['name', 'address'], 'string', 'max' => 255],
            [['phone', 'phone2'], 'number'],
            [['email'], 'string', 'max' => 200],
            ['email', 'email'],
            ['status', 'boolean'],
            [['rooms', 'status'], 'filter', 'filter' => 'intval'],
            // required on
            [[/*'name',*/ 'phone', /*'email',*/ 'rooms', /*'description',*/ 'address', 'metro_array', 'status'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * Редактировать
     *
     * @param WantPass $model
     * @return bool
     */
    public function update(WantPass $model)
    {
        $model->setAttributes($this->getAttributes(), false);

        return $model->save(false);
    }

    /**
     * Возвращает массив "Типов аренды"
     * @return array презентирующий "rent_types" атрибут
     */
    public function getRent_types_array()
    {
        return Json::decode($this->rent_types);
    }

    /**
     * Устанавливает "rent_types" атрибут
     * @param array $values массив "Типов аренды"
     */
    public function setRent_types_array($values)
    {
        $this->rent_types = Json::encode($values);
    }

    /**
     * Возвращает массив "Метро станций"
     * @return array презентирующий "metro" атрибут
     */
    public function getMetro_array()
    {
        return Json::decode($this->metro);
    }

    /**
     * Устанавливает "metro" атрибут
     * @param array $values массив "Метро станций"
     */
    public function setMetro_array($values)
    {
        $this->metro = Json::encode($values);
    }
}
