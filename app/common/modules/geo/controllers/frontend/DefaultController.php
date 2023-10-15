<?php

namespace common\modules\geo\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\modules\geo\models\City;
use common\modules\partners\models\frontend\search\AdvertSearch;

/**
 * Главный гео контроллер
 * @package common\modules\geo\controllers\frontend
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/geo/views/frontend';

        return true;
    }

    /**
     * Перенаправление на поддомен города
     * @return Response
     */
    public function actionPreIndex(): Response
    {
        $city = City::findByNameEng(Yii::$app->request->get('city_code'));

        if (!$city) {
            $city = City::findById(Yii::$app->getModule('geo')->mskCityId);
        }

        $redirect = [
            '/geo/default/index',
            'city' => $city->name_eng,
        ];

        $queryParams = Yii::$app->request->queryParams;
        $queryParams['city'] = $city->name_eng;

        unset($queryParams['city_name'], $queryParams['city_code']);
        return $this->redirect(array_merge($redirect, $queryParams), 302);
    }

    /**
     * Общая карта
     *
     * @param null $city
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionIndex($city = null): Response
    {
        $city = City::findByNameEng($city ?: 'msk');

        if (!$city) {
            throw new NotFoundHttpException();
        }
        $params = Yii::$app->request->get();
        $params['city_id'] = $city->city_id;
        $searchModel = new AdvertSearch();
        $dataProvider = $searchModel->search($params);
        $cityName = Yii::$app->request->getCurrentCitySubDomain();
        $currentCity = City::findByNameEng($cityName ?: 'msk');

        return $this->response($this->render('map.twig', ['city' => $city, 'currentCity'=>$currentCity, 'searchModel' => $searchModel]));
    }
}
