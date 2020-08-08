<?php

namespace common\modules\realty\models\backend\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\realty\models\RentType;

/**
 * Class RentTypeSearch
 * @package common\modules\realty\models\backend\search
 */
class RentTypeSearch extends RentType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rent_type_id', 'visible', 'sort'], 'integer'],
            [['name', 'slug', 'icons', 'short_description', 'meta_title', 'meta_description', 'meta_keywords', 'agency_rules'], 'safe'],
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
        $query = RentType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rent_type_id' => $this->rent_type_id,
            'visible' => $this->visible,
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'icons', $this->icons])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'meta_title', $this->meta_title])
            ->andFilterWhere(['like', 'meta_description', $this->meta_description])
            ->andFilterWhere(['like', 'meta_keywords', $this->meta_keywords])
            ->andFilterWhere(['like', 'agency_rules', $this->agency_rules]);

        return $dataProvider;
    }
}
