<?php

namespace frontend\modules\partners\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\modules\geo\models\City;
use common\modules\geo\models\Country;
use common\modules\users\models\Profile;
use common\modules\realty\models\RentType;
use common\modules\articles\models\Article;
use common\modules\helpdesk\models\Helpdesk;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models as models;
use frontend\modules\partners\models\Apartment;
use common\modules\partners\models\MetroStations;
use frontend\modules\partners\models\AdvertisementSlider;

/**
 * Главный контроллер партнеров
 * @package frontend\modules\partners\controllers
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $actions = ['apartments', 'preview', 'create', 'update'];

        if (in_array($action->id, $actions)) {
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {
                $userType = Yii::$app->BasisFormat->helper('Status')->getItem(Profile::getUserTypeArray(), Yii::$app->user->identity->profile->user_type);
                Yii::$app->session->setFlash('danger', '<b>Внимание:</b> <br/> Ваш тип аккаунта: "' . $userType . '" и Вы не можете производить данное действие. ');
                $this->redirect(['/office/default/index']);

                return false;
            }
        }

        if ($action->id == 'region') {
            Url::remember('', 'region');
        }
        if ($action->id == 'preview') {
            Url::remember('', 'preview');
        }

        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['help', 'index', 'pre-region', 'region', 'view', 'preview', 'thumbs', 'others', 'delete'],
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                    [
                        'actions' => ['apartments', 'create', 'update', 'calendar', 'buy-ads', 'preview', 'delete'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../caching/default.php'));
    }

    /**
     * Доска объявлений
     * @return string
     */
    public function actionIndex()
    {
        $cities = models\Apartment::alphCities();

        $searchModel = new models\search\AdvertSearch();

        return $this->render('index.twig', [
            'cities' => $cities,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Перенаправление на поддомен города
     */
    public function actionPreRegion()
    {
        $city = Yii::$app->request->get('city');

        if ($city) {
            $city = City::findByName($city);
        }

        if (!$city) {
            $city = City::findById(Yii::$app->getModule('geo')->mskCityId);
        }

        $redirect = [
            '/partners/default/region',
            'city' => $city->name_eng,
        ];

        $queryParams = Yii::$app->request->queryParams;
        $queryParams['city'] = $city->name_eng;

        $this->redirect(array_merge($redirect, $queryParams), 302);
    }

    /**
     * Просмотр объявлений определенного города исходя из поддомена
     * @return mixed
     * @throws \Exception
     */
    public function actionRegion()
    {
        $city = City::findByNameEng(Yii::$app->request->get('city'));

        if (!$city) {
            throw new HttpException(404, 'Страница по данному адресу не существует');
        }

        $params = Yii::$app->request->get();
        $params['city_id'] = $city->city_id;
        $articlesQuery = Article::find()->orderBy(['date_create' => SORT_DESC]);
        if (!is_null($city)) {
            $articlesQuery->where(['city' => $city]);
        } else {
            $articlesQuery->where('city IS NULL');
        }
        $articles = $articlesQuery->limit(1)->asArray()->all();

        $articlesQuery2 = Article::find()
            ->orderBy(['date_create' => SORT_DESC]);

        if (!is_null($city)) {
            $articlesQuery2->where(['city' => $city]);
        } else {
            $articlesQuery2->where('city IS NULL');
        }
        $articlesall3 = $articlesQuery2->limit(9)->asArray()->all();
        $i = 0;
        $articlesall2 = [];
        foreach ($articlesall3 as $item) {
            if ($i > 0) {
                $articlesall2[] = $item;
            }
            $i++;
        }
        $searchModel = new models\search\AdvertSearch();
        $dataProvider = $searchModel->search($params);
        $topAdverts = models\Advert::findAdvertsByCity($city->city_id, true, Yii::$app->request->get('sect'));

        Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://' . $city->name_eng . '.cdaem.ru')]);

        return $this->render('region.twig', [
            'city' => $city,
            'searchModel' => $searchModel,
            'topAdverts' => $topAdverts,
            'dataProvider' => $dataProvider,
            'articles' => $articles,
            'articlesall2' => $articlesall2,
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
            throw new NotFoundHttpException('The requested page does not exist.');
            //throw new NotFoundHttpException('Объявление под номером ' . $id . ' не найдено в базе данных');
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

    public function actionPreview($id)
    {
        $model = models\form\ApartmentForm::findByIdThisUser($id);

        return $this->render('preview.twig', [
            'model' => $model,
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
     * Объявления
     * @return string
     */
    public function actionApartments()
    {
        $filter = isset(Yii::$app->request->queryParams['filter']) ? (string)Yii::$app->request->queryParams['filter'] : '';
        $filter = explode(';', $filter);
        $filterArray = [];

        foreach ($filter as $param) {
            $param = explode('=', $param);
            if (isset($param[0]) && isset($param[1])) {
                $filterArray[$param[0]] = $param[1];
            }
        }
        $searchModel = new models\search\ApartmentSearch();
        $dataProvider = $searchModel->search($filterArray);

        return $this->render('apartments.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Добавить объявление
     * @return string|Response
     */
    public function actionCreate()
    {
        $apartment = new models\form\ApartmentForm(['scenario' => 'user-create']);
        $rentTypes = models\form\AdvertForm::getPreparedRentTypesAdvertsList(RentType::rentTypeslist(), $apartment->adverts);
        $advert = new models\form\AdvertForm(['scenario' => 'user-create']);
        $image = new models\form\ImageForm(['scenario' => 'user-create']);
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $advert->load(Yii::$app->request->post());
            $apartment->load(Yii::$app->request->post());
            $image->load(Yii::$app->request->post());
            $errors = array_merge(ActiveForm::validate($apartment), ActiveForm::validate($advert), ActiveForm::validate($image));

            if (!$errors) {
                $apartment->populateRelation('adverts', $advert);
                $apartment->populateRelation('images', $image);

                if (Yii::$app->request->post('submit')) {
                    if ($apartment->save(false)) {
                        Yii::$app->session->setFlash('success', 'Ваше объявление успешно добавлено в нашу базу данных.');

                        return $this->redirect(['/partners/default/apartments']);
                    } else {
                        return [
                            'status' => 0,
                            'message' => 'При добавлении объявления возникла ошибка, пожалуйста попробуйте еще раз или обратитесь в техническую поддержку.',
                        ];
                    }
                }

                return [];
            }

            return $errors;
        }

        return $this->render('apartment_create.twig', [
            'apartment' => $apartment,
            'advert' => $advert,
            'rentTypes' => $rentTypes,
            'image' => $image,
        ]);
    }

    /**
     * Редактировать объявление
     * @param $id
     * @return array|string
     * @throws HttpException
     */
    public function actionUpdate($id)
    {
        $apartment = models\form\ApartmentForm::findByIdThisUser($id);
        if (!$apartment) {
            throw new HttpException(404, 'Вы ищете страницу, которой не существует');
        }
        $apartment->scenario = 'user-update';
        $rentTypes = models\form\AdvertForm::getPreparedRentTypesAdvertsList(RentType::rentTypeslist(), $apartment->adverts);
        $advert = new models\form\AdvertForm(['scenario' => 'user-update']);
        $image = new models\form\ImageForm(['scenario' => 'user-update']);

        // Включаем активные чекбоксы объявлений
        $advert->rent_type = array_map(function ($n) {
            return $n->rent_type;
        }, $apartment->adverts);


        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $apartment->load(Yii::$app->request->post());
            $advert->load(Yii::$app->request->post());
            $image->load(Yii::$app->request->post());

            $errors = array_merge(ActiveForm::validate($apartment), ActiveForm::validate($advert), ActiveForm::validate($image));

            if (!$errors) {
                $apartment->populateRelation('adverts', $advert);
                $apartment->populateRelation('images', $image);

                if (Yii::$app->request->post('submit')) {
                    if ($apartment->save(false)) {
                        Yii::$app->session->setFlash('success', 'Ваше объявление обновлено.');

                        return $this->redirect(['/partners/default/apartments']);
                    } else {
                        return [
                            'status' => 0,
                            'message' => 'При добавлении объявления возникла ошибка, пожалуйста попробуйте еще раз или обратитесь в техническую поддержку.',
                        ];
                    }
                }

                return [];
            }

            return $errors;
        }


        return $this->render('apartment_update.twig', [
            'apartment' => $apartment,
            'advert' => $advert,
            'rentTypes' => $rentTypes,
            'image' => $image,
        ]);
    }

    public function actionDelete($id)
    {
        $apartment = models\form\ApartmentForm::findByIdThisUser($id);
        $oldAdverts = Advert::find()
            ->Where('apartment_id = :apartment_id', [':apartment_id' => $id])
            ->all();

        if ($oldAdverts) {
            foreach ($oldAdverts as $oldAdvert) {
                $oldAdvert->delete();
            }
        }
        $apartment->delete();

        return $this->redirect(['/partners/default/apartments']);
    }

    /**
     * Быстрое заселение (Календарь)
     * @return string
     */
    public function actionCalendar()
    {
        $apartments = Apartment::findApartmentsByAvailable(Yii::$app->user->id);

        return $this->render('calendar.twig', [
            'apartments' => $apartments
        ]);
    }

    /**
     * Находит ApartmentAdverts model по полю advert_id
     * Незабаненного пользователя
     * @param $advert_id
     * @param null $city_name_eng
     * @return array|null|\yii\db\ActiveRecord
     * @throws HttpException
     */
    protected function findAdvert($advert_id, $city_name_eng = null)
    {
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
            ->andFilterWhere(['name_eng' => $city_name_eng])
            ->one();
        if (!$advert) {
            throw new HttpException(404, 'Вы ищете страницу, которой не существует');
        }

        return $advert;
    }
}
