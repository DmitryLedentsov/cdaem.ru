<?php

namespace backend\modules\merchant\models;

use yii\data\ActiveDataProvider;
use Yii;

/**
 * Class PaymentSearch
 * @package backend\modules\merchant\models
 */
class PaymentSearch extends Payment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'payment_id'], 'integer'],
            [['module', 'type', 'date', 'system'], 'safe'],
            ['system', 'in', 'range' => array_keys(self::getSystems())],
            ['type', 'in', 'range' => array_keys($this->typeArray)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return parent::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 's';
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Payment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'payment_id' => $this->payment_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'system' => $this->system,
            'date(date)' => $this->date,
        ]);


        return $dataProvider;
    }


    /**
     * @return mixed
     */
    public static function getSystems()
    {
        $result = [];
        $systems = Yii::$app->getModule('merchant')->systems;
        $modules = array_keys($systems);

        foreach ($modules as $module) {
            $result = array_merge($result, $systems[$module]);
        }

        $result = Yii::$app->BasisFormat->helper('Status')->getList($result);

        return $result;
    }
}