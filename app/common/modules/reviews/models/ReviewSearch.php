<?php

namespace common\modules\reviews\models;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\partners\models\Apartment;

/**
 * Review Search
 * @package common\modules\reviews\models
 */
class ReviewSearch extends Review
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['apartment_id', 'integer']
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
        return '';
    }

    /**
     * Создать data provider instance
     *
     * @param array $params
     * @param bool $user
     *
     * @return ActiveDataProvider
     */
    public function search($params, $user = false)
    {
        $query = Review::find();

        if ($user) {
            $query->joinWith([
                'apartment' => function ($query) {
                    $query->where(Apartment::tableName() . '.user_id = :user_id', [':user_id' => Yii::$app->user->id]);
                },
            ]);
        }

        $query->moderation();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.apartment_id' => $this->apartment_id,
        ]);

        return $dataProvider;
    }
}
