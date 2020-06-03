<?php

namespace common\modules\seo\models\backend;

use common\modules\seo\models\Seotext;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * Seotext Search
 * @package common\modules\seo\models\backend
 */
class SeotextSearch extends Model
{
    public $url;
    public $type;
    public $visible;

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
    public function rules()
    {
        return [
            [['url'], 'string'],
            ['type', 'in', 'range' => array_keys(self::getTypeArray())],
            ['visible', 'boolean'],
        ];
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
        $query = Seotext::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'text_id' => SORT_DESC,
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
            'type' => $this->type,
            'visible' => $this->visible,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function getTypeArray()
    {
        return Seotext::getTypeArray();
    }
}