<?php

namespace common\modules\agency\models\backend\search;

use common\modules\agency\models\DetailsHistory;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * Details History Search
 * @package common\modules\agency\models\backend\search
 */
class DetailsHistorySearch extends DetailsHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['phone'], 'number'],
            [['email'], 'string', 'max' => 200],
            ['processed' , 'boolean'],
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
                    'processed' => SORT_ASC,
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
            'id' => $this->id,
            'processed' => $this->processed,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email]);


        return $dataProvider;
    }
}
