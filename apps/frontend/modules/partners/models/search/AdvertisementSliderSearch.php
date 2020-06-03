<?php

namespace frontend\modules\partners\models\search;

use frontend\modules\partners\models\AdvertisementSlider;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

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