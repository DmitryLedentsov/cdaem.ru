<?php

namespace common\modules\agency\models\backend\form;

use Yii;
use common\modules\agency\models\Advert;
use common\modules\agency\models\Apartment;

/**
 * Advert Form
 * @package common\modules\agency\models\backend\form
 */
class AdvertForm extends \yii\base\Model
{
    public $advert_id;

    public $meta_title;

    public $meta_description;

    public $meta_keywords;

    public $apartment_id;

    public $rent_type;

    public $price;

    public $currency;

    public $text;

    public $main_page;

    public $info;

    public $rules;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Advert())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['meta_title', 'meta_description', 'meta_keywords', 'apartment_id', 'rent_type', 'price', /*'currency',*/ 'text', 'main_page', 'info', 'rules'],
            'update' => ['meta_title', 'meta_description', 'meta_keywords', 'apartment_id', 'price', /*'currency',*/ 'text', 'main_page', 'info', 'rules'],
            'delete' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_id', 'rent_type', 'currency', 'main_page'], 'integer'],
            ['apartment_id', 'exist', 'targetClass' => '\common\modules\agency\models\Apartment', 'targetAttribute' => 'apartment_id'],
            ['rent_type', 'exist', 'targetClass' => '\common\modules\realty\models\RentType', 'targetAttribute' => 'rent_type_id'],
            [['meta_title', 'meta_description', 'meta_keywords'], 'string'],
            ['currency', 'in', 'range' => array_keys(Advert::getCurrencyList())],
            ['main_page', 'boolean'],
            ['text', 'string', 'max' => 255],
            [['price'], 'number'],
            [['rent_type'], 'unique', 'targetClass' => '\common\modules\agency\models\Advert',
                'targetAttribute' => ['apartment_id', 'rent_type'],
                'message' => 'Для этих апартаментов уже есть такой тип аренды',
            ],
            // required on
            [['meta_title', 'meta_description', 'meta_keywords', 'rent_type', 'price', 'currency', 'main_page'], 'required', 'on' => 'create'],
            [['meta_title', 'meta_description', 'meta_keywords', 'price', 'currency', 'main_page'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * Создать
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = new Advert();
            $model->setAttributes($this->getAttributes(), false);

            if (!$model->save(false)) {
                throw new \Exception('Advert not save');
            }

            $apartment = Apartment::findOne($this->apartment_id);
            $apartment->date_update = date('Y-m-d H:i:s');

            if (!$apartment->save(false)) {
                throw new \Exception('Apartment not save');
            }

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }
    }

    /**
     * Редактировать
     *
     * @param Advert $model
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function update(Advert $model)
    {
        $transaction = Yii::$app->db->beginTransaction();

        $model->setAttributes($this->getAttributes(), false);

        if (!$model->save(false)) {
            throw new \Exception('Advert not save');
        }

        $this->advert_id = $model->advert_id;

        $apartment = Apartment::findOne($model->apartment_id);
        $apartment->date_update = date('Y-m-d H:i:s');

        if (!$apartment->save(false)) {
            throw new \Exception('Apartment not save');
        }

        $transaction->commit();

        return true;
    }

    /**
     * Удалить
     *
     * @param Advert $model
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function delete(Advert $model)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model->delete();
            $apartment = Apartment::findOne($model->apartment_id);
            $apartment->date_update = date('Y-m-d H:i:s');
            $apartment->save(false);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }
    }
}
