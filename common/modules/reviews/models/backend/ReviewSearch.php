<?php

namespace common\modules\reviews\models\backend;

use common\modules\reviews\models\Review;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * Review Search
 * @package common\modules\reviews\models\backend
 */
class ReviewSearch extends Model
{
    public $review_id;
    public $apartment_id;
    public $user_id;
    public $match_description;
    public $price_quality;
    public $cleanliness;
    public $entry;
    public $visible;
    public $moderation;
    public $date_create;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['review_id', 'apartment_id', 'user_id', 'match_description', 'price_quality', 'cleanliness', 'entry', 'visible'], 'integer'],

            ['date_create', 'date', 'format' => 'php:Y-m-d'],

            ['match_description', 'in', 'range' => array_keys(Review::getRatingMatchDescriptionArray())],
            ['price_quality', 'in', 'range' => array_keys(Review::getRatingPriceAndQualityArray())],
            ['cleanliness', 'in', 'range' => array_keys(Review::getRatingCleanlinessArray())],
            ['entry', 'in', 'range' => array_keys(Review::getRatingEntryArray())],

            [['visible', 'moderation'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Review())->attributeLabels(), [

        ]);
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
        $query = Review::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'moderation' => SORT_ASC,
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
            'apartment_id' => $this->apartment_id,
            'user_id' => $this->user_id,
            'match_description' => $this->match_description,
            'price_quality' => $this->price_quality,
            'cleanliness' => $this->cleanliness,
            'entry' => $this->entry,
            'visible' => $this->visible,
            'moderation' => $this->moderation,
            'date(date_create)' => $this->date_create,
        ]);

        // $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}