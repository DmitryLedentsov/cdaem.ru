<?php

namespace common\modules\agency\models\backend\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\agency\models\Select;
use common\modules\realty\models\Apartment as TotalApartment;

/**
 * Select Search
 * @package common\modules\agency\models\backend\search
 */
class SelectSearch extends Select
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_select_id', 'rooms', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['phone', 'phone2'], 'number'],
            [['email'], 'string', 'max' => 200],
            [['description'], 'string'],
            [['rent_types', 'description', 'metro'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
            /*'sort' => [
                'defaultOrder' => [
                    'processed' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],*/
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'apartment_select_id' => $this->apartment_select_id,
            'rooms' => $this->rooms,
            'status' => $this->status,
        ]);

        if ($this->phone) {
            $query->andWhere('phone LIKE :phone OR phone2 LIKE :phone', [':phone' => '%' . $this->phone . '%']);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'rent_types', $this->rent_types])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'metro', $this->metro]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public static function getRoomsList()
    {
        return TotalApartment::getRoomsArray();
    }
}
