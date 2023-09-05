<?php

namespace common\modules\geo\controllers\frontend;

use Yii;
use yii\db\Query;
use yii\base\Model;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\modules\geo\models\City;
use common\modules\geo\models\Metro;
use yii\base\InvalidConfigException;
use common\modules\geo\models\CityByIpCache;
use common\modules\agency\models\Apartment as ApartmentAgency;
use common\modules\partners\models\frontend\Apartment as ApartmentPartners;

/**
 * Ajax гео контроллер
 * @package common\modules\geo\controllers\frontend
 */
class AjaxController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->enableCsrfValidation = false;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['X-Request-With'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        $this->module->viewPath = '@common/modules/geo/views/frontend';

        return true;
    }

    /**
     * Карта московского метро
     * @return string
     */
    public function actionMetroMsk(): string
    {
        return $this->renderAjax('metro-msk.html');
    }

    /**
     * Список популярных городов
     * @return Response
     */
    public function actionGetPopularCities() : Response
    {
        $cities = City::find()
            ->innerJoinWith('country')
            ->andWhere(['is_popular' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->successAjaxResponse(null, array_reduce($cities, function (array $result, City $city) {
            $result[] = [
                'city_id' => $city['city_id'],
                'name' => $city['name'],
            ];

            return $result;
        }, []));
    }

    /**
     * Поиск городов по имени
     * @return Response
     */
    public function actionSelectCity() : Response
    {
        $name = trim(Yii::$app->request->get('name'));

        if (mb_strlen($name) < 2) {
            return $this->successAjaxResponse(null, []);
        }

        $cities = City::find()
            ->innerJoinWith('country')
            ->andWhere(['like', City::tableName() . '.name', $name])
            ->orderBy(['name' => SORT_ASC])
            ->limit(15)
            ->all();

        $result = [];
        foreach ($cities as $city) {
            $result[] = [
                'city_id' => $city['city_id'],
                'name' => $city['name'],
                'country' => $city['country']['name'],
                'name_eng' => $city['name_eng'],
                'has_metro' => $city->hasMetro(),
            ];
        }

        return $this->successAjaxResponse(null, $result);
    }

    /**
     * Поиск городов по имени через API dadata.ru
     * @return Response
     */
    public function actionSelectCityByApi() : Response
    {
        $name = trim(Yii::$app->request->get('name'));

        if (mb_strlen($name) < 2) {
            return $this->successAjaxResponse(null, []);
        }

        $result = [];

        $dadata = new \Dadata\DadataClient(Yii::$app->params['dadata']['token'], Yii::$app->params['dadata']['secret']);

        $rowList = $dadata->suggest("address", $name, 5, [
            "locations" => [ "city_type_full" => "город" ],
            "from_bound"=> [ "value" => "city" ],
            "to_bound"=> [ "value"=> "city" ],
        ]);

        foreach ($rowList as $row) {
            $data = $row["data"];

            $city = City::findOne(['name' => $data["city"]]);
            $cityId = $city ? $city->city_id : '';

            $result[] = [
                "name" => $data["city"],
                "kladr_id" => $data["kladr_id"],
                "region" => $data["region"],
                "region_type_full" => $data["region_type_full"],
                "geo_lat" => $data["geo_lat"],
                "geo_lon" => $data["geo_lon"],
                "city_id" => $cityId
            ];
        }

        return $this->successAjaxResponse(null, $result);
    }

    /**
     * Поиск адреса через API dadata.ru
     * @return Response
     */
    public function actionSelectAddressByApi() : Response
    {
        $query = trim(Yii::$app->request->get('query'));
        $kladr = trim(Yii::$app->request->get('kladr'));

        if (mb_strlen($query) < 2) {
            return $this->successAjaxResponse(null, []);
        }

        $result = [];
        $dadata = new \Dadata\DadataClient(Yii::$app->params['dadata']['token'], Yii::$app->params['dadata']['secret']);

        $rowList = $dadata->suggest("address", $query, 5, [
            "locations" => [ "kladr_id" => "$kladr" ],
            "restrict_value" => true // Адрес без района и города
        ]);

        foreach ($rowList as $row) {
            $data = $row["data"];

            $result[] = [
                "value" => $row["value"],
                "geo_lat" => $data["geo_lat"],
                "geo_lon" => $data["geo_lon"],
            ];
        }

        return $this->successAjaxResponse(null, $result);
    }

    /**
     * Поиск города по ip через API dadata.ru
     * @return Response
     */
    public function actionGetCityByIp() : Response
    {
        $ip = Yii::$app->request->userIP;
        // $ip = "46.226.227.20";
        // $ip = "1.1.1.1";

        $cacheRecord = CityByIpCache::findOne(['ip' => $ip]);
        // $cacheRecord = null;

        if ($cacheRecord) {
            $cityId = $cacheRecord->city_id;
            $city = City::findOne(['city_id' => $cityId]);
            $cityName = $city ? $city->name : null;
        } else {
            $dadata = new \Dadata\DadataClient(Yii::$app->params['dadata']['token'], Yii::$app->params['dadata']['secret']);

            $result = $dadata->iplocate($ip);
            $cityName = is_array($result) ? ArrayHelper::getValue($result, 'data.city') : '';

            $city = City::findByName($cityName) ?: null;
            $cityId = $city ? $city->city_id : null;

            $cacheRecord = new CityByIpCache();
            $cacheRecord->ip = $ip;
            $cacheRecord->city_id = $cityId;
            $cacheRecord->save(false);
        }


        return $this->successAjaxResponse(null, [
            'cityId' => $cityId,
            'city' => $cityName ?: 'Похоже вы не из России'
        ]);
    }

    /**
     * Данные объявления
     * @param string $typeId
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionApartment(string $typeId): Response
    {
        $type = mb_substr($typeId, 0, 1, 'utf-8');
        $id = explode('_', $typeId);
        $id = (isset($id[1]) ? (int)$id[1] : null);

        if ($type === 'a') {
            $model = ApartmentAgency::find()
                ->joinWith(['titleImage', 'adverts' => function (Query $query):void {
                    $query->select(['advert_id', 'apartment_id', 'rent_type', 'price']);
                    $query->andWhere(['rent_type' => 1]);
                    $query->orWhere(['rent_type' => 2]);
                    $query->orWhere(['rent_type' => 3]);
                    $query->joinWith(['rentType']);
                }])
                ->where([ApartmentAgency::tableName() . '.apartment_id' => $id])
                ->visible()
                ->one();
        } else {
            $model = ApartmentPartners::find()
                ->joinWith(['titleImage', 'user', 'adverts' => function (Query $query):void {
                    $query->select(['advert_id', 'apartment_id', 'rent_type', 'price', 'currency']);
                    $query->andWhere(['rent_type' => 1]);
                    $query->orWhere(['rent_type' => 2]);
                    $query->orWhere(['rent_type' => 3]);
                    $query->joinWith(['rentType']);
                }, 'city' => function ($query) {
                    $query->joinWith('country');
                }])
                ->where([ApartmentPartners::tableName() . '.apartment_id' => $id])
                ->permitted()
                ->one();
        }

        if (!$model instanceof ApartmentAgency && !$model instanceof ApartmentPartners) {
            return $this->response('Нет данных');
        }

        $advertsHtml = '';
        foreach ($model->adverts as $advert) {
            $name = $advert->rentType->short_name;
            $price = 'Нет указано';

            if ($model instanceof ApartmentAgency) {
                $currency = 'rub';
                $url = Url::to(['/agency/default/view', 'id' => $advert->advert_id]);
            } else {
                $currency = $advert->currencyName;
                $url = Url::to(['/partners/default/view', 'id' => $advert->advert_id, 'city' => $model->city->name_eng]);
            }

            if ($advert->price > 0) {
                $price = Yii::$app->formatter->asCurrency($advert->price, $currency);
            }

            $advertsHtml .= '<p><a href="' . $url . '" target="_blank"><span>' . $name . ':</span> ' . $price . '</a></p>';
        }

        return $this->response('<div class="balloon-info"><div class="clearfix">' .
            '<div class="balloon-view">' .
            '<div class="balloon-image" style="background-image: url(' . $model->getTitleImageSrc() . ')"></div>' .
            '</div>' .
            '<div class="balloon-desc">' . $advertsHtml . '</div></div>' .
            '<p class="balloon-property">Этаж: ' . $model->floor . ', ' . $model->roomsName . ', Ремонт: ' . $model->remontName . '</p>' .
            '</div>');
    }

    /**
     * Карта московского метро
     * @return Response
     */
    public function actionMap(): Response
    {
        $city = City::findByNameEng(Yii::$app->request->get('city_code') ?: 'msk');
        $rentType = Yii::$app->request->get('sect');
        $priceStart = Yii::$app->request->get('price_start');
        $priceEnd = Yii::$app->request->get('price_end');
        $rooms = Yii::$app->request->get('room');
        $metroWalk = Yii::$app->request->get('metro_walk');
        $remont = Yii::$app->request->get('remont');
        $floor = Yii::$app->request->get('floor');

        $search = function ($apartmentModel) use ($city, $floor, $remont, $metroWalk, $rooms, $rentType, $priceStart, $priceEnd): array {
            return $apartmentModel::find()->filterWhere(['city_id' => $city->city_id,'total_rooms'=> $rooms>0 ?: '', 'metro_walk'=>$metroWalk, 'remont'=>$remont])
                ->andWhere('floor '.($floor === '1' ? '= 1' : ($floor === '2' ? '> 1' : ($floor === '3' ? '< number_floors' : ''))))
                ->joinWith(['titleImage', 'adverts' => function (Query $query) use ($priceEnd, $priceStart, $rentType) : void {
                    $query->select(['advert_id', 'apartment_id', 'rent_type', 'price' ]);
                    if ($rentType) {
                        $query->andWhere(['rent_type' => $rentType]);
                    }
                    if ($priceStart) {
                        $query->andWhere(['>=','price', $priceStart]);
                    }
                    if ($priceEnd) {
                        $query->andWhere(['<=', 'price', $priceEnd]);
                    }
                    $query->joinWith(['rentType']);
                }])
                ->visible()
                ->all();
        };
        $apartmentsAgency = $search(ApartmentAgency::class);
        $apartmentsPartners = $search(ApartmentPartners::class);
        $result = [
            'type' => 'FeatureCollection',
            'features' => [],
            'request'=>Yii::$app->request->get(),
        ];

        foreach ($apartmentsAgency as $apartmentAgency) {
            $result['features'][] = $this->getFeatureInfo($apartmentAgency);
        }

        foreach ($apartmentsPartners as $apartmentPartners) {
            $result['features'][] = $this->getFeatureInfo($apartmentPartners);
        }

        return $this->successAjaxResponse(null, $result);
    }

    /**
     * @param Model $apartment
     * @return array
     */
    private function getFeatureInfo(Model $apartment): array
    {
        return [
            'id' => (($apartment instanceof ApartmentAgency) ? 'a' : 'p') . '_' . $apartment->apartment_id,
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$apartment->latitude, $apartment->longitude],
            ],
            'properties' => [
                'clusterCaption' => $apartment->address,
                'hintContent' => $apartment->address,
            ],
            'options' => [
                'preset' => ($apartment instanceof ApartmentAgency) ? 'islands#darkGreenIcon' : 'islands#blueDotIcon',
            ]
        ];
    }

    public function actionNearestStations(string $cityName, float $latitude, float $longitude): Response
    {
        $city = City::findByName($cityName);
        $cityId = $city ? $city->city_id : null;
        $nearestStations = $cityId ? Metro::getNearestStationsByCoords($cityId, $latitude, $longitude) : [];

        return $this->successAjaxResponse(null, $nearestStations);
    }
}
