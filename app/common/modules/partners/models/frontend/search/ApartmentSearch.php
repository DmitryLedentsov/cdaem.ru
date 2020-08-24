<?php

namespace common\modules\partners\models\frontend\search;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use common\modules\realty\models\RentType;
use common\modules\partners\models\frontend\Apartment;

/**
 * @inheritdoc
 */
class ApartmentSearch extends Apartment
{
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['slug']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'slug' => 'Тип аренды',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['slug', 'in', 'range' => array_keys($this->rentTypesList), 'message' => 'Url адрес неправильный'],
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
    public function search($params)
    {
        $query = Apartment::find()->distinct()
            ->joinWith([
                'adverts' => function ($query) {
                    $query->joinWith('rentType');
                },
                'city' => function ($query) {
                    $query->with('country');
                },
            ])
            ->with([
                'titleImage',
                'reviews',
            ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'visible' => SORT_DESC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            Apartment::tableName() . '.user_id' => Yii::$app->user->id,
            'slug' => isset($params['slug']) ? $params['slug'] : null,
        ]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function getRentTypesList()
    {
        $rentTypes = RentType::find()
            ->select(['slug', 'name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($rentTypes, 'slug', 'name');
    }
}
