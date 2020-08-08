<?php

namespace common\modules\agency\models\backend\search;

use yii\data\ActiveDataProvider;
use common\modules\agency\models\Advert;
use common\modules\realty\models\RentType;
use common\modules\agency\models\Apartment;

/**
 * Поисковая модель апартаментов
 * @package common\modules\agency\models\backend\search
 */
class ApartmentSearch extends Apartment
{
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['adverts.rent_type']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'adverts.rent_type' => 'Типы аренды',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_id', 'user_id', 'city_id', 'closest_city_id', 'apartment', 'district1', 'district2', 'floor', 'total_rooms', 'total_area', 'visible', 'remont', 'metro_walk'], 'integer'],
            [['description', 'date_create', 'date_update'], 'safe'],
            [['address'], 'string'],
            ['adverts.rent_type', 'in', 'range' => array_keys($this->rentTypesList)],
        ];
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
        $query = Apartment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
                ],
            ],
        ]);

        $query->distinct()->joinWith([
            'adverts' => function ($query) {
                $query->with(['rentType']);
            },
        ])->with([
            'titleImage',
            'city' => function ($query) {
                $query->with('country');
            },
            'mainDistrict',
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            Apartment::tableName() . '.apartment_id' => $this->apartment_id,
            Apartment::tableName() . '.user_id' => $this->user_id,
            Apartment::tableName() . '.city_id' => $this->city_id,
            Apartment::tableName() . '.closest_city_id' => $this->closest_city_id,
            Apartment::tableName() . '.apartment' => $this->apartment,
            Apartment::tableName() . '.district1' => $this->district1,
            Apartment::tableName() . '.district2' => $this->district2,
            Apartment::tableName() . '.floor' => $this->floor,
            Apartment::tableName() . '.total_rooms' => $this->total_rooms,
            Apartment::tableName() . '.total_area' => $this->total_area,
            Apartment::tableName() . '.visible' => $this->visible,
            Apartment::tableName() . '.remont' => $this->remont,
            Apartment::tableName() . '.metro_walk' => $this->metro_walk,
            Apartment::tableName() . '.date_create' => $this->date_create,
            Apartment::tableName() . '.date_update' => $this->date_update,
            Advert::tableName() . '.rent_type' => $this->getAttribute('adverts.rent_type'),
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    /**
     * Список типов аренды
     */
    public function getRentTypesList()
    {
        return RentType::rentTypesList();
    }
}
