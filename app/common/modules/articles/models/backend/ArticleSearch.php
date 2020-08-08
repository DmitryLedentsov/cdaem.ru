<?php

namespace common\modules\articles\models\backend;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\articles\models\Article;

/**
 * Article Search
 * @package common\modules\articles\models\backend
 */
class ArticleSearch extends Model
{
    public $name;

    public $short_text;

    public $slug;

    public $title;

    public $description;

    public $keywords;

    public $title_img;

    public $full_text;

    public $visible;

    public $status;

    public $article_id;

    public $date_create;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'visible', 'status'], 'integer'],
            [['date_create'], 'date', 'format' => 'php:Y-m-d'],
            [['date_create'], 'string'],
            [['name', 'short_text', 'slug', 'title', 'description', 'keywords', 'title_img'], 'string', 'max' => 255],
            [['full_text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Article())->attributeLabels(), [

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
        $query = Article::find();

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
            'article_id' => $this->article_id,
            'visible' => $this->visible,
            'status' => $this->status,
            'date(date_create)' => $this->date_create,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title_img', $this->title_img])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_text', $this->short_text])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'full_text', $this->full_text]);

        return $dataProvider;
    }
}
