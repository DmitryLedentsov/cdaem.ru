<?php

namespace frontend\modules\partners\models\search;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * @inheritdoc
 */
class ServiceSearch extends \common\modules\partners\models\Service
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = self::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'process' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 15,
            ],
        ]);

        /*$this->load($params);

        if (!$this->validate()) {
            // Ну тут уже явно зачем показывать если плохая адресная строка
            $query->where('0=1');
            return $dataProvider;
        }*/

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);

        $query->andWhere(['is not', 'payment_id', null]);

        return $dataProvider;
    }
}
