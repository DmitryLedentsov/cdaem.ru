<?php

namespace common\modules\agency\models\search;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use common\modules\geo\models\Metro;
use common\modules\agency\models\Advert;
use common\modules\geo\models\Districts;
use common\modules\realty\models\RentType;
use common\modules\agency\models\Apartment;
use common\modules\agency\models\query\AdvertQuery;
use common\modules\realty\models\Apartment as TotalApartment;

/**
 * @inheritdoc
 */
class AdvertSearch extends Advert
{
    /**
     * @var
     */
    public $rentType;

    /**
     * @var
     */
    public $rooms;

    /**
     * @var
     */
    public $district;

    /**
     * @var
     */
    public $metro_array;

    /**
     * Список доступных вариантов количества комнат
     */
    public function getRoomsList()
    {
        $roomsList = ['all' => 'Кол-во комнат'];

        return ArrayHelper::merge($roomsList, TotalApartment::getRoomsArray());
    }

    /**
     * Список округов города Москвы
     */
    public function getDistrictsList()
    {
        $districts = ArrayHelper::map($this->districtsArray, 'name_eng', 'district_name');

        return ArrayHelper::merge(['all' => 'Выбрать округ'], $districts);
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
     * Массив округов города Москвы
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getDistrictsArray()
    {
        return Districts::find()
            ->where(['city_id' => \Yii::$app->getModule('agency')->mskCityId])
            ->asArray()
            ->all();
    }

    /**
     * Список доступных рент тайпов
     * В отличии от rentTypesList, отдает вместо rent_type_id - slug
     * Переписано из-за нетривиальной задачи с slug'ами в строке запроса
     * @return array
     */
    public function getRentSlugsList()
    {
        return ArrayHelper::map($this->rentTypesArray, 'slug', 'name');
    }

    /**
     * Массив доступных рент тайпов
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getRentTypesArray()
    {
        return RentType::find()
            ->asArray()
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rentType', 'in', 'range' => array_keys($this->rentSlugsList)],
            ['rooms', 'in', 'range' => array_keys($this->roomsList)],
            ['district', 'in', 'range' => array_keys($this->districtsList)],
            ['metro_array', 'each', 'rule' => ['in', 'range' => array_keys($this->mskMetroList)]],
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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        //Дефолтный поиск агентских квартир
        $this->rentType = 'kvartira_na_chas';
        $this->rooms = 'all';
        $this->district = 'all';
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /*if (isset($params['metro'])) {
            $metro_array = explode(',', $params['metro']);
            foreach ($metro_array as &$metro) {
                $metro = (int)$metro;
            }
            $params['metro_array'] = $metro_array;
        }*/

        $query = Advert::previewFind()->mainPage();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->orderBy('advert_id ASC');

        if ($this->rooms != 'all') {
            $query->andFilterWhere(['total_rooms' => $this->rooms]);
        }

        if ($this->district && $this->district != 'all') {
            $query->andWhere('district1=:district OR district2=:district', [':district' => ArrayHelper::map($this->districtsArray, 'name_eng', 'id')[$this->district]]);
        }

        if (!empty($this->metro_array)) {
            $query->andWhere(['IN', 'metro_id', $this->metro_array]);
        }

        $query->andFilterWhere(['rent_type' => ArrayHelper::map($this->rentTypesArray, 'slug', 'rent_type_id')[$this->rentType]]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function allSearch($params)
    {
        /** @var AdvertQuery $query */
        $query = Advert::previewFind()->mainPage();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $district = $this->district === 'all' ? null : Districts::findByAlias($this->district);

        $query->andFilterWhere([
            'rent_type' => [
                ArrayHelper::map($this->rentTypesArray, 'slug', 'rent_type_id')[$this->rentType],
                ArrayHelper::map($this->rentTypesArray, 'slug', 'rent_type_id')['zagorodniy_dom']
            ],
        ]);

        if ($this->rooms !== 'all') {
            $query->andFilterWhere([
                Apartment::tableName() . '.total_rooms' => $this->rooms,
            ]);
        }

        if ($district) {
            $query->andWhere([
                'or',
                [
                    Apartment::tableName() . '.district1' => $district->id,
                ],
                [
                    Apartment::tableName() . '.district2' => $district->id,
                ]
            ]);
        }

        return $dataProvider;
    }
}
