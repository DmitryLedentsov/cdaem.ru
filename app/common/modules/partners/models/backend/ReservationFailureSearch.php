<?php

namespace common\modules\partners\models\backend;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReservationFailureSearch represents the model behind the search form about `common\modules\partners\models\ReservationFailure`.
 * @package common\modules\partners\models\backend
 */
class ReservationFailureSearch extends ReservationFailure
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        return array_merge($attributeLabels, [
            'closed' => 'Обработанные',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'reservation_id'], 'integer'],
            ['conflict', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
            ['processed', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
            ['cause_text', 'string'],
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
        $query = ReservationFailure::find();

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
            self::tableName() . '.reservation_id' => $this->reservation_id,
            self::tableName() . '.conflict' => $this->conflict,
            self::tableName() . '.processed' => $this->processed,
        ]);

        $query->andFilterWhere(['like', 'cause_text', $this->cause_text]);

        return $dataProvider;
    }
}
