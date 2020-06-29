<?php

namespace common\modules\agency\models\backend\search;

use common\modules\agency\models\Apartment;
use common\modules\agency\models\Advert;
use common\modules\agency\models\Advertisement;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * Поисковая модель рекламных объявлений
 * @package backend\modules\agency\models
 */
class AdvertisementSearch extends Advertisement
{
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['advert.apartment.apartment_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertisement_id', 'advert_id'], 'integer'],
            [['advert.apartment.apartment_id'], 'integer'],
            ['text', 'string', 'max' => 255],
            ['date_expire', 'date', 'format' => 'php:Y-m-d'],
            [['date_expire'], 'string'],
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
        $query = Advertisement::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_expire' => SORT_DESC,
                ],
            ],
        ]);

        $query->joinWith([
            'advert' => function ($query) {
                $query->joinWith([
                    'apartment',
                ]);
            }
        ]);

        $dataProvider->sort->attributes['advert.apartment.address'] = [
            'asc' => [Apartment::tableName() . '.apartment_id' => SORT_ASC],
            'desc' => [Apartment::tableName() . '.apartment_id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'advertisement_id' => $this->advertisement_id,
            Advert::tableName() . '.advert_id' => $this->advert_id,
            Apartment::tableName() . '.apartment_id' => $this->getAttribute('advert.apartment.apartment_id'),
        ]);


        if ($this->date_expire) {
            $query->andWhere(['<=', 'date_expire', $this->date_expire]);
        }

        $query->andFilterWhere(['like', Advertisement::tableName() . '.text', $this->text]);

        return $dataProvider;
    }
}
