<?php

namespace common\modules\partners\models\frontend\search;

use Yii;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use common\modules\partners\models\ReservationsPayment;
use common\modules\partners\models\frontend\Reservation;

/**
 * @inheritdoc
 */
class ReservationSearch extends Reservation
{
    /**
     * Все или только открытые
     * @var
     */
    public $find;

    /**
     * Сокращенное имя города (субдомен)
     * @var
     */
    public $city;

    /**
     * Имя города вводимое пользователем
     * @var
     */
    public $city_name;

    /**
     * Планируемый бюджет
     * @var
     */
    public $budget;

    /**
     * Список доступных значений свойства $find
     * @var array
     */
    public $findList = ['all', 'open'];

    /**
     * Список критерия поиска Бюджет
     * @var array
     */
    public $budgetList = [
        1 => 'Меньше 50',
        2 => 'от 50 до 100',
        3 => 'от 100 до 500',
        4 => 'от 500 до 1000',
        5 => 'от 1000 до 2000',
        6 => 'от 2000 до 5000',
        7 => 'от 5000 до 10000',
        8 => 'от 10000 до 20000',
        9 => 'от 20000 и больше',
    ];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'budget' => 'Бюджет клиента',
            'city_name' => 'Город',
            'currency' => 'Валюта'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['find', 'in', 'range' => $this->findList, 'enableClientValidation' => false],
            ['rent_type', 'in', 'range' => array_keys($this->rentTypesList), 'enableClientValidation' => false],
            ['budget', 'in', 'range' => array_keys($this->budgetList), 'enableClientValidation' => false],
            ['currency', 'in', 'range' => array_keys($this->currencyArray), 'enableClientValidation' => false],
            [['city'], 'safe', 'enableClientValidation' => false],
            ['city_name', 'string', 'enableClientValidation' => false]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'renter' => [],
            'owner' => ['find', 'rent_type', 'city', 'city_name', 'budget', 'currency'],
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
    public function search($params = [])
    {
        $query = $this->ownerQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'closed' => SORT_ASC,
                    'date_create' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        if (!empty($params['find']) and $params['find'] == 'open') {
            return $dataProvider;
        } // Поиск не нужен

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'rent_type' => $this->rent_type,
            'currency' => $this->currency
        ]);

        // Критерий поиска Город
        if ($this->city) {
            if (Yii::$app->request->cityModel) {
                $this->city_name = Yii::$app->request->cityModel->name;
                $query->andWhere(['city_id' => Yii::$app->request->cityModel->city_id]);
            } else {
                $query->where('0=1');

                return $dataProvider;
            }
        }

        // Критерий поиска бюджет
        if ($this->budget) {
            $this->addBudgetQuery($query);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function wantRentSearch($params = [])
    {
        $query = Reservation::find()
            ->where([
                Reservation::tableName() . '.user_id' => Yii::$app->user->id,
            ])->with('payments');


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

        return $dataProvider;
    }

    /**
     * Возвращает ActiveQuery глобальных заявок
     * @param $params - массив GET данных запроса
     * @return ActiveQuery
     */
    private function ownerQuery($params)
    {
        if (empty($params['find']) or $params['find'] == 'all') {
            $query = Reservation::find()
                ->joinWith([
                    'user' => function ($query) {
                        $query->banned(0)->with('profile');
                    },
                ])
                ->andWhere(['!=', Reservation::tableName() . '.user_id', Yii::$app->user->id])
                ->lastThreeMonths();
        } elseif ($params['find'] == 'open') {
            $query = Reservation::find()
                ->joinWith([
                    'user' => function ($query) {
                        $query->banned(0)->with('profile');
                    },
                    'payments'
                ], true, 'RIGHT JOIN')
                ->andWhere([
                    ReservationsPayment::tableName() . '.user_id' => Yii::$app->user->id
                ]);
        } else {
            $query = Reservation::find()
                ->where('0=1');
        }

        return $query;
    }

    protected function addBudgetQuery($query)
    {
        $params = [
            1 => [':start' => 50],
            2 => [':start' => 50, ':end' => 100],
            3 => [':start' => 100, ':end' => 500],
            4 => [':start' => 500, ':end' => 1000],
            5 => [':start' => 1000, ':end' => 2000],
            6 => [':start' => 2000, ':end' => 5000],
            7 => [':start' => 5000, ':end' => 10000],
            8 => [':start' => 10000, ':end' => 20000],
            9 => [':end' => 20000],
        ];

        if ($this->budget == 1) {
            $query->andWhere('money_to <= :start', $params[1]);

            return;
        }

        if ($this->budget == 9) {
            $query->andWhere('money_from >= :end', $params[9]);

            return;
        }

        if ($this->budget > 1 and $this->budget < 9) {
            $query->andWhere(['OR',
                'money_from <= :start AND :start <= money_to',
                'money_from <= :end AND :end <= money_to',
                ':start <= money_from AND money_from <= :end',
                ':start <= money_to AND money_to <= :end',
            ], $params[$this->budget]);

            return;
        }

        return;
    }
}
