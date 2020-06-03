<?php

namespace backend\modules\partners\models;

use common\modules\realty\models\RentType;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * @inheritdoc
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
            [['apartment_id', 'user_id', 'city_id', 'closest_city_id', 'apartment', 'floor', 'total_rooms', 'total_area', 'visible', 'status', 'remont', 'metro_walk', 'suspicious'], 'integer'],
            [['description', 'date_create', 'date_update'], 'safe'],
            [['address'], 'string'],
            ['adverts.rent_type', 'in', 'range' => array_keys($this->rentTypesList)],
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
        $query = Apartment::find()->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        
        $query->joinWith([
                'user' => function ($query) {
                    $query->joinWith([
                        'profile' => function ($query) {
                            $query->partner(\common\modules\users\models\Profile::NO_PARTNER);
                        },
                    ]);
                },
                'adverts' => function ($query) {
                    $query->with(['rentType']);
                },
            ])->with([
                'titleImage',
                'city' => function ($query) {
                    $query->with('country');
                },
            ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            Apartment::tableName().'.apartment_id' => $this->apartment_id,
            Apartment::tableName().'.user_id' => $this->user_id,
            Apartment::tableName().'.city_id' => $this->city_id,
            Apartment::tableName().'.closest_city_id' => $this->closest_city_id,
            Apartment::tableName().'.apartment' => $this->apartment,
            Apartment::tableName().'.floor' => $this->floor,
            Apartment::tableName().'.total_rooms' => $this->total_rooms,
            Apartment::tableName().'.total_area' => $this->total_area,
            Apartment::tableName().'.visible' => $this->visible,
            Apartment::tableName().'.status' => $this->status,
            Apartment::tableName().'.remont' => $this->remont,
            Apartment::tableName().'.metro_walk' => $this->metro_walk,
            Apartment::tableName().'.date_create' => $this->date_create,
            Apartment::tableName().'.date_update' => $this->date_update,
            Apartment::tableName().'.suspicious' => $this->suspicious,
            Advert::tableName().'.rent_type' => $this->getAttribute('adverts.rent_type'),
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
    
    /**
     * Спиоск типов аренды
     */
    public function getRentTypesList()
    {
        return RentType::rentTypesList();
    }
}
