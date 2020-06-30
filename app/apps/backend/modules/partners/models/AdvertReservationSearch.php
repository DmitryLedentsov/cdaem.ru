<?php

namespace backend\modules\partners\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\realty\models\RentType;

/**
 * AdvertReservationSearch represents the model behind the search form about `backend\modules\partners\models\Reservation`.
 */
class AdvertReservationSearch extends AdvertReservation
{
    /**
     * Актуальность: Да или нет
     * @var int
     */
    public $actuality;

    /**
     * Город объявления
     * @var int
     */
    public $city_id;

    /**
     * Тип аренды объявления
     * @var int
     */
    public $rent_type;

    /**
     * Не заезд
     * @var
     */
    public $failed;

    /**
     * Список Типов аренды
     * @return array
     */
    public function getRentTypesList()
    {
        return RentType::rentTypeslist();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        return array_merge($attributeLabels, [
            'city_id' => 'Город',
            'rent_type' => 'Тип аренды',
            'failed' => 'Незаезд',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'advert_id', 'city_id', 'landlord_id'], 'integer'],
            [['actuality', 'children', 'pets', 'closed', 'failed'], 'boolean'],
            ['rent_type', 'in', 'range' => array_keys($this->rentTypesList)],
            ['clients_count', 'in', 'range' => array_keys($this->clientsCountArray)],
            ['cancel', 'in', 'range' => array_keys($this->cancelList)],
            ['confirm', 'in', 'range' => array_keys($this->confirmList)],
            [['more_info', 'cancel_reason'], 'string', 'max' => 255],
            [['date_arrived', 'date_out', 'date_create', 'date_actuality'], 'safe'],
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
        $query = AdvertReservation::find()
            ->joinWith([

                'user' => function ($query) {
                    $query->joinWith('profile');
                },
                'advert' => function ($query) {
                    $query->joinWith([
                        'rentType',
                        'apartment' => function ($query) {
                            $query->joinWith([
                                'titleImage',
                                'city' => function ($query) {
                                    $query->joinWith('country');
                                }
                            ]);
                        },
                    ]);

                },
                'failure',
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.user_id' => $this->user_id,
            self::tableName() . '.landlord_id' => $this->landlord_id,
            self::tableName() . '.advert_id' => $this->advert_id,
            self::tableName() . '.children' => $this->children,
            self::tableName() . '.pets' => $this->pets,
            self::tableName() . '.clients_count' => $this->clients_count,
            self::tableName() . '.closed' => $this->closed,
            self::tableName() . '.confirm' => $this->confirm,
        ]);

        if ($this->rent_type) {
            $query->andWhere([Advert::tableName() . '.rent_type' => $this->rent_type]);
        }

        if ($this->city_id) {
            $query->andWhere([Apartment::tableName() . '.city_id' => $this->city_id]);
        }

        if ($this->actuality == 1) {
            $query->andWhere(['>', self::tableName() . '.date_actuality', date('Y-m-d H:i:s')]);
        }

        if ($this->actuality === '0') {
            $query->andWhere(['<', self::tableName() . '.date_actuality', date('Y-m-d H:i:s')]);
        }

        if ($this->cancel) {
            $query->andWhere(['!=', self::tableName() . '.cancel', 0]);
        }

        if ($this->cancel === '0') {
            $query->andWhere([self::tableName() . '.cancel' => 0]);
        }

        if ($this->failed) {
            $query->andWhere([Failure::tableName() . '.id NOT NULL']);
        }

        return $dataProvider;
    }
}
