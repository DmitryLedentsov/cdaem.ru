<?php

namespace common\modules\partners\controllers\frontend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\modules\geo\models\City;
use common\modules\partners\models\frontend\Advert;
use common\modules\partners\models\frontend as models;

/**
 * Главный контроллер партнеров
 * @package common\modules\partners\controllers\frontend
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

        $this->module->viewPath = '@common/modules/partners/views/frontend';

        if ($action->id == 'region') {
            Url::remember('', 'region');
        }
        if ($action->id == 'preview') {
            Url::remember('', 'preview');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'pre-region', 'region', 'view', 'thumbs', 'others'],
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                ]
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching/default.php'));
    }

    /**
     * Доска объявлений (все города)
     * @return Response
     */
    public function actionIndex(): Response
    {

        $cities = models\Apartment::alphCities();

        $searchModel = new models\search\AdvertSearch();

        return $this->response($this->render('search.twig', [
            'cities' => $cities,
            'searchModel' => $searchModel,
        ]));
    }

    /**
     * Перенаправление на поддомен города
     * @return Response
     */
    public function actionPreRegion(): Response
    {
        $city = City::findByNameEng(Yii::$app->request->get('city_code'));

        if (!$city) {
            $city = City::findById(Yii::$app->getModule('geo')->mskCityId);
        }

        $redirect = [
            '/partners/default/region',
            'city' => $city->name_eng,
        ];

        $queryParams = Yii::$app->request->queryParams;
        $queryParams['city'] = $city->name_eng;

        unset($queryParams['city_name'], $queryParams['city_code']);

        // dd(self::class, $queryParams);

        return $this->redirect(array_merge($redirect, $queryParams), 302);
    }

    /**
     * Просмотр объявлений определенного города исходя из поддомена
     * @return mixed
     * @throws \Exception
     */
    public function actionRegion()
    {
        if (!$city = City::findByNameEng(Yii::$app->request->get('city'))) {
            throw new HttpException(404, 'Страница по данному адресу не существует');
        }

        $params = Yii::$app->request->get();
        $params['city_id'] = $city->city_id;

        $searchModel = new models\search\AdvertSearch();
        $dataProvider = $searchModel->search($params);
        $topAdverts = models\Advert::findAdvertsByCity($city->city_id, true, Yii::$app->request->get('sect'));

        Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://' . $city->name_eng . '.cdaem.ru')]);
        $cityName = Yii::$app->request->getCurrentCitySubDomain();
        $currentCity = City::findByNameEng($cityName ?: 'msk');

        // throw new \Exception("Чтобы не вернул контроллер, будет редирект");
        return $this->render('region.twig', [
            'city' => $city,
            'searchModel' => $searchModel,
            'topAdverts' => $topAdverts,
            'dataProvider' => $dataProvider,
            'currentCity' => $currentCity,
        ]);
    }

    /**
     * Просмотр
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = models\Advert::getFullData($id, Yii::$app->request->get('city'));

        if (!$model) {
            throw new NotFoundHttpException('Объявление под номером ' . $id . ' не найдено в базе данных');
        }
        $res = [];
        if ($model->apartment->user->profile->vip) {
            $otherAdverts = models\Advert::getOtherAdverts($model->apartment->user_id, $id);

            $res = ArrayHelper::map(
                $otherAdverts,
                'advert_id',
                function ($element) {
                    return $element;
                },
                function ($element) {
                    return $element['rentType']['slug'] . ',' . $element['rentType']['name'];
                }
            );
        }

        return $this->render('view.twig', [
            'model' => $model,
            'otherAdverts' => $res,
        ]);
    }

    /**
     * Остальные предложения арендодателя
     * @return string
     * @throws HttpException
     */
    public function actionOthers()
    {
        $model = $this->findAdvert(Yii::$app->request->get('id'), Yii::$app->request->get('city'));

        if (!$model->apartment->user->profile->vip) {
            throw new HttpException(404, 'Вы запросили страницу, которой не существует');
        }

        $otherAdverts = models\Advert::getOtherAdverts($model->apartment->user_id);
        $res = ArrayHelper::map(
            $otherAdverts,
            'advert_id',
            function ($element) {
                return $element;
            },
            function ($element) {
                return $element['rentType']['slug'] . ',' . $element['rentType']['name'];
            }
        );

        return $this->render('others.twig', [
            'model' => $model,
            'otherAdverts' => $res,
        ]);
    }

    /**
     * Находит ApartmentAdverts model по полю advert_id
     * Незабаненного пользователя
     *
     * @param $advert_id
     * @param string|null $cityNameEng
     * @return Advert
     * @throws HttpException
     */
    protected function findAdvert($advert_id, ?string $cityNameEng = null): Advert
    {
        /** @var Advert $advert */
        $advert = models\Advert::find()
            ->joinWith([
                'apartment' => function ($query) {
                    $query->joinWith([
                        'city',
                        'user' => function ($query) {
                            $query->banned(0);
                        },
                    ]);
                },
            ])
            ->where(['advert_id' => $advert_id])
            ->andFilterWhere(['name_eng' => $cityNameEng])
            ->one();

        if (!$advert) {
            throw new HttpException(404, 'Вы ищете страницу, которой не существует');
        }

        return $advert;
    }
}
