<?php

namespace common\modules\seo\models\backend;

use yii\data\ActiveDataProvider;
use common\modules\seo\models\SeoSpecification;
use yii\base\Model;
use Yii;

/**
 * Class SeoSpecificationSearch
 * @package backend\modules\partners\models
 */
class SeoSpecificationSearch extends Model
{
    public $id;
    public $city;
    public $url;
    public $title;
    public $description;
    public $keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['city', 'url', 'title', 'description', 'keywords'], 'string'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SeoSpecification::find();

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
            return $dataProvider;
        }

        /*
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        */

        $query->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'keywords', $this->keywords]);

        return $dataProvider;
    }
}