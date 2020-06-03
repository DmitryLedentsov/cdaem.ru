<?php

namespace common\modules\callback\models\backend;

use common\modules\callback\models\Callback;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * Callback Search
 * @package common\modules\callback\models\backend
 */
class CallbackSearch extends Model
{
    public $callback_id;
    public $active;
    public $phone;
    public $number;
    public $date_create;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callback_id', 'active'], 'integer'],
            ['phone', 'number'],
            [['date_create'], 'date', 'format' => 'php:Y-m-d'],
            [['date_create'], 'string'],
        ];
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
        $query = Callback::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'active' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'callback_id' => $this->callback_id,
            'active' => $this->active,
            'date(date_create)' => $this->date_create,
        ]);

        $query->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}