<?php

namespace frontend\modules\partners\models\search;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use common\modules\geo\models\Metro;
use common\modules\realty\models\RentType;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models\Apartment;
use common\modules\realty\models\Apartment as TotalApartment;

/**
 * @inheritdoc
 */
class AdvertSearch extends Advert
{
    /**
     * @var
     */
    public $sect;

    /**
     * @var
     */
    public $rooms;

    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $price_start;

    /**
     * @var
     */
    public $price_end;

    /**
     * @var
     */
    public $city_id;

    /**
     * @var
     */
    public $metro;

    /**
     * @var
     */
    public $sort;

    /**
     * @var
     */
    public $online_user;

    /**
     * @var
     */
    public $now_available;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['online_user']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'online_user' => 'Только с онлайн владельцами',
            'now_available' => 'Только свободные',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->sect = 2;
        $this->type = 1;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'in', 'range' => [1, 2]], //Сдам/сниму
            ['sect', 'in', 'range' => array_keys($this->rentTypesList)],
            ['rooms', 'in', 'range' => array_keys($this->roomsList)],
            [['price_start', 'price_end'], 'integer', 'integerOnly' => true, 'min' => 0],
            ['price_start', 'compare', 'compareAttribute' => 'price_end', 'operator' => '<=', 'when' => function ($model) {
                return $model->price_end;
            }],
            [['now_available', 'sort', 'online_user'], 'boolean'],
            ['city_id', 'safe'], //city_id берется не из $_GET, а из БД
            ['metro', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['metro', 'in', 'range' => array_keys($this->metroStationsList)],
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
     * Список доступных вариантов количества комнат
     */
    public function getRoomsList()
    {
        $roomsList = TotalApartment::getRoomsArray();
        array_unshift($roomsList, 'Кол-во комнат');

        return $roomsList;
    }

    /**
     * Список станций метро этого города $this->city_id
     */
    public function getMetroStationsList()
    {
        $metro = Metro::find()
            ->where(['city_id' => $this->city_id])
            ->select(['metro_id', 'name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($metro, 'metro_id', 'name');
    }

    /**
     * Список метро станций города Москва
     * @return array
     */
    public function getMskMetroList()
    {
        return Metro::getMskMetroArray();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Advert::find()
            ->distinct()
            ->top(false)
            ->joinWith([
                'apartment' => function ($query) {
                    $query->permitted()
                        ->joinWith([
                            'metroStation' => function ($query) {
                                $query->with('metro');
                            },
                            'user' => function ($query) {
                                if ($this->online_user) {
                                    $query->online();
                                }
                                $query->banned(0);
                            },
                        ])
                        ->with([
                            'city',
                            'titleImage',
                            'alternateTitleImage',
                            'adverts' => function ($query) {
                                $query->with('rentType');
                            },
                        ]);
                },
            ])
            ->with('rentType');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => $this->module->pageSize,
                'pageSize' => $this->module->pageSize,
            ],
        ]);

        $this->city_id = $params['city_id'];
        $this->load($params);


        $query->andWhere(['city_id' => $this->city_id]);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->orderBy('CASE WHEN `position` = 0 THEN 1 ELSE 0 END, `position` ASC');

        if ($this->sort == 1) {
            $query->orderBy('price ASC');
        } elseif ($this->sort == 2) {
            $query->orderBy('price DESC');
        }

        if ($this->rooms != 0) {
            $query->andFilterWhere(['total_rooms' => $this->rooms]);
        }

        $query->andFilterWhere(['rent_type' => $this->sect]);
        $query->andFilterWhere(['>=', 'price', $this->price_start]);
        $query->andFilterWhere(['<=', 'price', $this->price_end]);
        $query->andFilterWhere(['metro_id' => $this->metro]);


        if ($this->now_available) {
            $query->andFilterWhere(['now_available' => $this->now_available]);
        }

        return $dataProvider;
    }

    /**
     * Упрощенный поиск главной страницы по Москве
     * @param $params
     * @return array of ApartmentAdverts instances, or an empty array if nothing matches.
     */
    public function siteSearch($params)
    {
        if (!isset($params['rentType'])) {
            $params['rentType'] = 'kvartira_na_sutki';
        }

        $rentType = RentType::findRentTypeBySlug($params['rentType']);

        if (!$rentType) {
            return [];
        }

        $metro_array = null;
        if (isset($params['metro'])) {
            $metro_array = explode(',', $params['metro']);
            foreach ($metro_array as &$metro) {
                $metro = (int)$metro;
            }
        }

        return static::find()->distinct()->joinWith([
            'apartment' => function ($query) {
                $query->permitted()
                    ->with([
                        'city',
                        'adverts' => function ($query) {
                            $query->with('rentType');
                        },
                        'titleImage',

                    ])
                    ->joinWith([
                        'user' => function ($query) {
                            $query->banned(0);
                        },
                        'metroStation' => function ($query) {
                            $query->with('metro');
                        }
                    ]);
            },
        ])
            ->with('rentType')
            ->andWhere(['rent_type' => $rentType['rent_type_id'], 'city_id' => Yii::$app->getModule('agency')->mskCityId])
            ->andFilterWhere(['IN', 'metro_id', $metro_array])
            ->orderBy('CASE WHEN `position` = 0 THEN 1 ELSE 0 END,  `position` ASC')
            ->limit(12)
            ->all();
    }
}
