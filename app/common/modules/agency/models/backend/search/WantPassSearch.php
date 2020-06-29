<?php

namespace common\modules\agency\models\backend\search;

use common\modules\realty\models\Apartment as TotalApartment;
use common\modules\agency\models\WantPass;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * WantPass Search
 * @package common\modules\agency\models\backend\search
 */
class WantPassSearch extends WantPass
{
    /**
     * Контактные телефоны
     */
    public $contact_phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apartment_want_pass_id', 'rooms', 'status', 'contact_phone'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
            [['phone', 'phone2'], 'number'],
            [['email'], 'string', 'max' => 200],
            [['description'], 'string'],
            [['rent_types', 'address', 'metro', 'images'], 'safe'],
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
            'apartment_want_pass_id' => $this->apartment_want_pass_id,
            'rooms' => $this->rooms,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'rent_types', $this->rent_types])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'metro', $this->metro])
            ->andFilterWhere(['like', 'images', $this->images]);

        if ($this->contact_phone) {
            $query->andWhere('phone LIKE :phone OR phone2 LIKE :phone', [':phone' => '%' . $this->contact_phone . '%']);
        }

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
