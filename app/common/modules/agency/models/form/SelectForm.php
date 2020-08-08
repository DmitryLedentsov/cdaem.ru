<?php

namespace common\modules\agency\models\form;

use yii\base\Model;
use yii\helpers\Json;
use common\modules\agency\models\Select;

/**
 * Форма, Быстро подберём квартиру
 * @package frontend\modules\agency\models\form
 */
class SelectForm extends Model
{
    public $name;

    public $email;

    public $rent_types_array;

    public $rooms;

    public $phone;

    public $description;

    public $metro_array;

    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Select)->attributeLabels(), [
            'verifyCode' => 'Защитный код',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rent_types_array', 'rooms', 'phone', 'metro_array'], 'required'],

            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],
            ['rent_types_array', 'default', 'value' => []],
            ['rent_types_array', 'each', 'rule' => ['in', 'range' => array_keys($this->rentTypesList)]],
            ['metro_array', 'default', 'value' => []],
            ['metro_array', 'each', 'rule' => ['in', 'range' => array_keys($this->metroStations)]],
            [['rooms'], 'integer'],
            ['rooms', 'in', 'range' => array_keys($this->roomsList)],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 200],
            ['email', 'email'],

            // Защитный код
            ['verifyCode', 'required', 'message' => 'Подтвердите, что Вы не робот'],
            ['verifyCode', \common\modules\site\widgets\Captcha::getClassValidator()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->phone = str_replace(['(', ')', '+', ' ', '-'], '', $this->phone);

        return true;
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Select();
        $model->setAttributes($this->getAttributes(), false);
        $model->metro = Json::encode($this->metro_array);
        $model->rent_types = Json::encode($this->rent_types_array);
        $model->date_create = date('Y-m-d H:i:s');

        return $model->save(false);
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getRentTypesList()
    {
        return Select::getRentTypesList();
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function getMetroStations()
    {
        return Select::getMetroStations();
    }

    /**
     * Список доступных вариантов количества комнат
     * @return array
     */
    public static function getRoomsList()
    {
        return Select::getRoomsList();
    }
}
