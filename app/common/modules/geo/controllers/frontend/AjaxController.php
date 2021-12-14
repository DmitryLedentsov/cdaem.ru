<?php

namespace common\modules\geo\controllers\frontend;

use common\modules\geo\models\Metro;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\Response;
use common\modules\geo\models\City;
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
    public function beforeAction($action)
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
    public function actionMetroMsk()
    {
        return $this->renderAjax('metro-msk.html');
    }

    /**
     * Поиск городов по имени
     * @return array|Response
     */
    public function actionSelectCity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $name = trim(Yii::$app->request->get('name'));

        if (mb_strlen($name) < 2) {
            return [];
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

        return $result;
    }

    /**
     * Данные объявления
     * @param $typeId
     * @return string
     */
    public function actionApartment($typeId)
    {
        $type = mb_substr($typeId, 0, 1, 'utf-8');
        $id = explode('_', $typeId);
        $id = (isset($id[1]) ? (int)$id[1] : null);

        if ($type === 'a') {
            $model = ApartmentAgency::find()
                ->joinWith(['titleImage', 'adverts' => function ($query) {
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
                ->joinWith(['titleImage', 'user', 'adverts' => function ($query) {
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
            return 'Нет данных';
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

        return '<div class="balloon-info"><div class="clearfix">' .
            '<div class="balloon-view">' .
            '<div class="balloon-image" style="background-image: url(' . $model->getTitleImageSrc() . ')"></div>' .
            '</div>' .
            '<div class="balloon-desc">' . $advertsHtml . '</div></div>' .
            '<p class="balloon-property">Этаж: ' . $model->floor . ', ' . $model->roomsName . ', Ремонт: ' . $model->remontName . '</p>' .
            '</div>';
    }

    /**
     * Карта московского метро
     * @return array
     */
    public function actionMap()
    {
        $apartmentsAgency = ApartmentAgency::find()
            ->joinWith(['titleImage', 'adverts' => function ($query) {
                $query->select(['advert_id', 'apartment_id', 'rent_type', 'price']);
                $query->andWhere(['rent_type' => 1]);
                $query->orWhere(['rent_type' => 2]);
                $query->orWhere(['rent_type' => 3]);
                $query->joinWith(['rentType']);
            }])
            ->visible()
            ->all();

        $apartmentsPartners = ApartmentPartners::find()
            ->joinWith(['titleImage', 'user', 'adverts' => function ($query) {
                $query->select(['advert_id', 'apartment_id', 'rent_type', 'price', 'currency']);
                $query->andWhere(['rent_type' => 1]);
                $query->orWhere(['rent_type' => 2]);
                $query->orWhere(['rent_type' => 3]);
                $query->joinWith(['rentType']);
            }])
            ->permitted()
            ->all();

        $result = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($apartmentsAgency as $apartmentAgency) {
            $result['features'][] = $this->getFeatureInfo($apartmentAgency);
        }

        foreach ($apartmentsPartners as $apartmentPartners) {
            $result['features'][] = $this->getFeatureInfo($apartmentPartners);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    /**
     * @param Model $model
     * @return array
     */
    private function getFeatureInfo(Model $model): array
    {
        return [
            'id' => (($model instanceof ApartmentAgency) ? 'a' : 'p') . '_' . $model->apartment_id,
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$model->latitude, $model->longitude],
            ],
            'properties' => [
                'clusterCaption' => $model->address,
                'hintContent' => $model->address,
            ],
            'options' => [
                'preset' => ($model instanceof ApartmentAgency) ? 'islands#darkGreenIcon' : 'islands#blueDotIcon',
            ]
        ];
    }

    public function actionNearestStations($cityId, $latitude, $longitude): array {
        Yii::$app->response->format = Response::FORMAT_JSON;

        //dd($cityId);

        return Metro::getNearestStationsByCoords($cityId, $latitude, $longitude);
        // return Metro::getNearestStationsByCoords($cityId, 37.641872, 55.766456);
    }
}
