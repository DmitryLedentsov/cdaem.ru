<?php

namespace common\modules\merchant\models\backend;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\partners\models\Service;

/**
 * Class ServiceSearch
 * @package common\modules\merchant\models\backend
 */
class ServiceSearch extends Service
{
    public $payment;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            ['service', 'in', 'range' => array_keys(Yii::$app->BasisFormat->helper('Status')->getList(Yii::$app->getModule('merchant')->systems['partners']))],
            ['payment', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
            [['date_start', 'date_expire'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 's';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Service())->attributeLabels(), [
            'payment' => 'Оплачено'
        ]);
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
        $query = Service::find()->with(['user', 'payment']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
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
            'user_id' => $this->user_id,
            'service' => $this->service,
        ]);

        if ($this->payment) {
            $query->andWhere('payment_id IS NOT NULL');
        }

        return $dataProvider;
    }
}
