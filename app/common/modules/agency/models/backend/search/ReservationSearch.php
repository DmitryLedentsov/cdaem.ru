<?php

namespace common\modules\agency\models\backend\search;

use common\modules\agency\models\Reservation;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * Reservation Search
 * @package common\modules\agency\models\backend\search
 */
class ReservationSearch extends Reservation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reservation_id', 'apartment_id', 'clients_count', 'transfer', 'whau'], 'integer'],
            [['name', 'email', 'more_info'], 'string', 'max' => 255],
            ['phone', 'number'],
            ['processed', 'boolean'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Reservation())->attributeLabels(), [
            'contact' => 'Контакты',
            'date_reserve' => 'Дата резервации'
        ]);
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'processed' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'reservation_id' => $this->reservation_id,
            'apartment_id' => $this->apartment_id,
            'clients_count' => $this->clients_count,
            'transfer' => $this->transfer,
            'date_arrived' => $this->date_arrived,
            'date_out' => $this->date_out,
            'whau' => $this->whau,
            'processed' => $this->processed,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'more_info', $this->more_info])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
