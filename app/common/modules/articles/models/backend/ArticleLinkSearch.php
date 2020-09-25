<?php

namespace common\modules\articles\models\backend;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\articles\models\ArticleLink;

/**
 * Article Search
 * @package common\modules\articles\models\backend
 */
class ArticleLinkSearch extends Model
{
    public $id;

    public $text;

    public $title;

    public $thumb_img;

    public $link_page;

    public $article_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'id'], 'integer'],
            [['text', 'title', 'link_page', 'thumb_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new ArticleLink())->attributeLabels(), [

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
        $query = ArticleLink::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
