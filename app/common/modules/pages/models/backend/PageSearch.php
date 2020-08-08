<?php

namespace common\modules\pages\models\backend;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\pages\models\Page;

/**
 * Page Search
 * @package common\modules\pages\models\backend
 */
class PageSearch extends Model
{
    public $name;

    public $title;

    public $description;

    public $keywords;

    public $text;

    public $page_id;

    public $status;

    public $active;

    public $url;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'status', 'active'], 'integer'],
            ['url', 'string', 'max' => 16],
            ['name', 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Page())->attributeLabels(), [

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
        $query = Page::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'page_id' => SORT_DESC
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
            'page_id' => $this->page_id,
            'status' => $this->status,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
