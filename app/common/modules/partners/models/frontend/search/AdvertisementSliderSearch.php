<?php

namespace common\modules\partners\models\frontend\search;

use common\modules\partners\models\frontend\AdvertisementSlider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @inheritdoc
 */
class AdvertisementSliderSearch extends AdvertisementSlider
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

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
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        $query = AdvertisementSlider::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'payment' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);

        return $dataProvider;
    }
}
