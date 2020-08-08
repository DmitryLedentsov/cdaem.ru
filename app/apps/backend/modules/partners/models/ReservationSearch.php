<?php

namespace backend\modules\partners\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\partners\models\Reservation;

/**
 * ReservationSearch represents the model behind the search form about `backend\modules\partners\models\Reservation`.
 */
class ReservationSearch extends Reservation
{
    /**
     * Актуальность: Да или нет
     * @var int
     */
    public $actuality;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'city_id',], 'integer'],
            [['actuality', 'children', 'pets', 'closed'], 'boolean'],
            ['rent_type', 'in', 'range' => $this->rentTypesList],
            ['clients_count', 'in', 'range' => $this->clientsCountArray],
            ['rooms', 'in', 'range' => $this->roomsList],
            ['beds', 'in', 'range' => $this->bedsList],
            ['floor', 'in', 'range' => $this->floorArray],
            ['metro_walk', 'in', 'range' => $this->metroWalkList],
            ['cancel', 'in', 'range' => $this->cancelList],
            [['address', 'more_info', 'cancel_reason'], 'string', 'max' => 255],
            [['date_arrived', 'date_out', 'date_create', 'date_actuality'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Reservation::find();

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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.user_id' => $this->user_id,
            self::tableName() . '.children' => $this->children,
            self::tableName() . '.pets' => $this->pets,
            self::tableName() . '.clients_count' => $this->clients_count,
            self::tableName() . '.rooms' => $this->rooms,
            self::tableName() . '.beds' => $this->beds,
            self::tableName() . '.floor' => $this->floor,
            self::tableName() . '.metro_walk' => $this->metro_walk,
            self::tableName() . '.date_arrived' => $this->date_arrived,
            self::tableName() . '.date_out' => $this->date_out,
            self::tableName() . '.closed' => $this->closed,
            self::tableName() . '.date_create' => $this->date_create,
            self::tableName() . '.date_actuality' => $this->date_actuality,
            self::tableName() . '.rent_type' => $this->rent_type,
            self::tableName() . '.city_id' => $this->city_id,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'more_info', $this->more_info])
            ->andFilterWhere(['like', 'cancel_reason', $this->cancel_reason]);

        if ($this->actuality == 1) {
            $query->andWhere(['>', self::tableName() . '.date_actuality', date('Y-m-d H:i:s')]);
        }

        if ($this->actuality === '0') {
            $query->andWhere(['<', self::tableName() . '.date_actuality', date('Y-m-d H:i:s')]);
        }

        if ($this->cancel) {
            $query->andWhere(['!=', self::tableName() . '.cancel', 0]);
        }

        if ($this->cancel === '0') {
            $query->andWhere([self::tableName() . '.cancel' => 0]);
        }

        return $dataProvider;
    }
}
