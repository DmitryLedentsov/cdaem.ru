<?php

namespace common\modules\agency\models\backend\form;

use yii\helpers\Json;
use common\modules\agency\models\Select;

/**
 * Select Form
 * @package common\modules\agency\models\backend\form
 */
class SelectForm extends \yii\base\Model
{
    public $apartment_select_id;

    public $name;

    public $phone;

    public $phone2;

    public $email;

    public $rent_types;

    public $rooms;

    public $description;

    public $metro;

    public $status;

    public $date_create;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Select())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['name', 'phone', 'phone2', 'email', 'rent_types_array', 'rooms', 'description', 'metro_array', 'status'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rent_types_array', 'each', 'rule' => ['in', 'range' => array_keys(Select::getRentTypesList())]],
            ['metro_array', 'each', 'rule' => ['in', 'range' => array_keys(Select::getMetroStations())]],
            ['rooms', 'in', 'range' => array_keys(Select::getRoomsList())],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            ['rooms', 'integer'],
            ['status', 'boolean'],
            [['phone', 'phone2'], 'number'],
            [['email'], 'email'],
            ['status', 'in', 'range' => [0, 1]],
            ['email', 'email'],
            [['rooms', 'status'], 'filter', 'filter' => 'intval'],
            [['name', 'phone', 'email', 'rent_types_array', 'rooms', 'description', 'metro_array', 'status'], 'required', 'on' => 'update']
        ];
    }

    /**
     * Редактировать
     *
     * @param Select $model
     * @return bool
     */
    public function update(Select $model)
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
