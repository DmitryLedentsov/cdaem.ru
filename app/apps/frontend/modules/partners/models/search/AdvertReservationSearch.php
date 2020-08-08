<?php

namespace frontend\modules\partners\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use frontend\modules\partners\models\AdvertReservation;

/**
 * @inheritdoc
 */
class AdvertReservationSearch extends AdvertReservation
{
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
        return [
            'landlord' => [],
            'renter' => [],
            'test' => [],
        ];
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


    ////////////

    public function AlltypetSearch($iduser = null)
    {
        $query = AdvertReservation::find()
            ->with([
                'advert' => function ($query) {
                    $query->with([
                        'apartment' => function ($query) {
                            $query->with([
                                'city' => function ($query) {
                                    $query->with('country');
                                },
                                'titleImage',
                            ]);
                        },
                    ]);
                },
            ])
            ->joinWith([
                'user' => function ($query) {
                    $query->banned(0)->with('profile');
                },
            ])
            ->andWhere(['!=', 'landlord_id', Yii::$app->user->id])
            ->andWhere(['confirm' => 2])
            ->andWhere(['user_id' => $iduser]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'closed' => SORT_ASC,
                    'cancel' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $query;
    }

    ///////////

    public function landlordSearch($params = [])
    {
        $query = AdvertReservation::find()
            ->with([
                'advert' => function ($query) {
                    $query->with([
                        'apartment' => function ($query) {
                            $query->with([
                                'city' => function ($query) {
                                    $query->with('country');
                                },
                                'titleImage',
                            ]);
                        },
                    ]);
                },
            ])
            ->joinWith([
                'user' => function ($query) {
                    $query->banned(0)->with('profile');
                },
            ])
            ->andWhere([self::tableName() . '.landlord_id' => Yii::$app->user->id]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'closed' => SORT_ASC,
                    //'cancel' => SORT_DESC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function renterSearch($params = [])
    {
        $query = AdvertReservation::find()
            ->joinWith([
                'advert' => function ($query) {
                    $query->joinWith([
                        'apartment' => function ($query) {
                            $query->joinWith([
                                'user' => function ($query) {
                                    $query->banned(0);
                                },
                            ])
                                ->with([
                                    'city' => function ($query) {
                                        $query->with('country');
                                    },
                                    'titleImage',
                                ]);
                        },
                    ]);
                },
            ])
            ->andWhere([self::tableName() . '.user_id' => Yii::$app->user->id]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    // 'closed' => SORT_ASC,
                    // 'cancel' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }
}
