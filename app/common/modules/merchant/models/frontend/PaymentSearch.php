<?php

namespace common\modules\merchant\models\frontend;

use Yii;
use yii\data\ActiveDataProvider;
use common\modules\merchant\models\Payment;

/**
 * @inheritdoc
 * @package common\modules\merchant\models\frontend
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
