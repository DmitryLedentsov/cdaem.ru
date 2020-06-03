<?php

namespace frontend\modules\merchant\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;
use Yii;

/**
 * @inheritdoc
 */
class PaymentSearch extends Payment
{
    /**
     * Создает экземпляр ActiveDataProvider с поисковым запросом
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        $query = Payment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 5,
            ],
        ]);

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);

        return $dataProvider;
    }
}