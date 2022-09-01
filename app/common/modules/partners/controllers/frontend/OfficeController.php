<?php

namespace common\modules\partners\controllers\frontend;

use common\modules\geo\models\City;
use common\modules\partners\models\frontend\Image;
use common\modules\realty\models\Apartment as ApartmentModel;
use common\modules\users\models\Profile;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\HttpException;
use yii\widgets\ActiveForm;
use common\modules\realty\models\RentType;
use common\modules\partners\models\frontend\Advert;
use common\modules\partners\models\frontend as models;
use common\modules\partners\models\frontend\Apartment;

/**
 * Class OfficeController
 * @package common\modules\partners\controllers\frontend
 */
class OfficeController extends \frontend\components\Controller
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

        $actions = ['apartments', 'preview', 'create', 'update', 'delete '];

        if (in_array($action->id, $actions)) {
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {

                $userType = Yii::$app->BasisFormat->helper('Status')->getItem(Profile::getUserTypeArray(), Yii::$app->user->identity->profile->user_type);

                Yii::$app->session->setFlash('danger', '<b>Внимание:</b> <br/> Ваш тип аккаунта: "' . $userType . '" и Вы не можете производить данное действие. ');

                $this->redirect(['/office/default/index']);

                return false;
            }
        }

        return true;
    }

    public function actionPreview($id)
    {
        $model = models\form\ApartmentForm::findByIdThisUser($id);

        return $this->render('preview.twig', [
            'model' => $model,
        ]);
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
                        'actions' => ['preview', /*'delete'*/],
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

        return array_merge($behaviors, require(__DIR__ . '/../../caching/office.php'));
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

        $aparts = $dataProvider->getModels();
        $apart = $aparts[0];
        $adverts = $apart->getAdverts()->all();

        // dd($apart);
        // dd($adverts);
        // foreach ($aparts as $apart) {
        // }

        // dd($apart->adverts);

        // $s = '';
        // foreach ($aparts as $apart) {
            // if ($apart->getActivePayedSliderAdvId()) {
            //     dd($apart->getActivePayedSliderAdvId());
            // }
            /*foreach ($apart->adverts as $advert) {
                $s .= ('' . $apart->apartment_id . ' ' . $advert->advert_id . '; ');
                if ($advert->advert_id === 7440) {
                    $sliders = $advert->advertSliders;

                    foreach ($sliders as $slider) {
                        if ($slider->visible && $slider->payment) {
                            dd($slider);
                        }
                    }


                }
            }*/
        // }

        // dd($s);

        return $this->render('apartments.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Добавить объявление
     * @return Response
     */
    public function actionCreate(): Response
    {
        $apartment = new models\form\ApartmentForm(['scenario' => 'user-create']);
        $rentTypes = models\form\AdvertForm::getPreparedRentTypesAdvertsList(RentType::rentTypeslist(), $apartment->adverts);
        $advert = new models\form\AdvertForm(['scenario' => 'user-create']);
        $image = new models\form\ImageForm(['scenario' => 'user-create']);
        $facility = new models\form\FacilityForm(['scenario' => 'user-create']);

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            // dd(self::class, $_POST, $_FILES);
            // dd(Yii::$app->request->post());
            // dd(Yii::$app->request->post(), $apartment, $result, $apartment->city_id);

            $apartment->load(Yii::$app->request->post());

            // dd(Yii::$app->request->post(), $apartment);

            $advert->load(Yii::$app->request->post());
            $image->load(Yii::$app->request->post());

            // Собираем удобства в массив
            $facilities = ArrayHelper::getValue(Yii::$app->request->post(), 'FacilityForm');

            if ($facilities) {
                $facility->load(['FacilityForm' => [
                    'facilities' => $facilities,
                ]]);
            }

            $errors = array_merge(
                $this->validate($apartment),
                $this->validate($advert),
                $this->validate($image),
                $this->validate($facility)
            );

            if (empty($errors)) {
                $apartment->populateRelation('adverts', $advert);
                $apartment->populateRelation('images', $image);
                $apartment->populateRelation('facilities', $facility);
                $apartment->save(false);

                Yii::$app->session->setFlash('success', 'Ваше объявление успешно добавлено в нашу базу данных.');

                return $this->redirect(['/partners/office/apartments']);
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('apartment_create.twig', [
            'apartment' => $apartment,
            'advert' => $advert,
            'rentTypes' => $rentTypes,
            'image' => $image,
             // 'cities' => City::dropDownList(),
            'popularCities' => City::getPopular(),
            'metroWalks' => ApartmentModel::getMetroWalkArray(),
            'currencies' => ApartmentModel::getCurrencyArray(),
            'rooms' => ApartmentModel::getRoomsArray(),
            'sleepingPlaces' => ApartmentModel::getSleepingPlacesArray(),
            'beds' => ApartmentModel::getBedsArray(),
            'remont' => ApartmentModel::getRemontArray(),
            'safety' => ApartmentModel::getSafetyArray(),
            'heating' => ApartmentModel::getHeatingArray(),
            'floorCovering' => ApartmentModel::getFloorCoveringArray(),
            'bathroom' => ApartmentModel::getBathroomArray(),
            'buildingType' => ApartmentModel::getBuildingTypeArray(),
        ]));
    }

    /**
     * Редактировать объявление
     * @param $id
     * @return array|\common\components\Response|Response
     * @throws HttpException
     */
    public function actionUpdate($id) : Response
    {
        $apartment = models\form\ApartmentForm::findByIdThisUser($id);
        if (!$apartment) {
            throw new HttpException(404, 'Вы ищете страницу, которой не существует');
        }

        // dd($apartment);
        // dd($apartment->images);

        $apartment->scenario = 'user-update';
        $rentTypes = models\form\AdvertForm::getPreparedRentTypesAdvertsList(RentType::rentTypeslist(), $apartment->adverts);
        $advert = new models\form\AdvertForm(['scenario' => 'user-update']);
        $image = new models\form\ImageForm(['scenario' => 'user-update']);
        $facility = new models\form\FacilityForm(['scenario' => 'user-update']);
        $city = City::findById($apartment->city_id);

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $apartment->load(Yii::$app->request->post());
            $advert->load(Yii::$app->request->post());
            $image->load(Yii::$app->request->post());
            $facilities = ArrayHelper::getValue(Yii::$app->request->post(), 'FacilityForm');

            // dd($facilities);

            if ($facilities) {
                $facility->load(['FacilityForm' => [
                    'facilities' => $facilities,
                ]]);
            }

            $errors = array_merge(
                $this->validate($apartment),
                $this->validate($advert),
                $this->validate($image),
                $this->validate($facility)
            );

            if (!$errors) {
                $apartment->populateRelation('adverts', $advert);
                $apartment->populateRelation('images', $image);
                $apartment->populateRelation('facilities', $facility);

                // if (Yii::$app->request->post('submit')) { // не работает при апдейте
                    if ($apartment->save(false)) {
                        Yii::$app->session->setFlash('success', 'Ваше объявление обновлено.');
                        return $this->redirect(['/partners/office/apartments']);
                    }

                    return [
                        'status' => 0,
                        'message' => 'При добавлении объявления возникла ошибка, пожалуйста попробуйте еще раз или обратитесь в техническую поддержку.',
                    ];
                // }
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        // старый шаблон
        /*return $this->render('apartment_update.twig', [
            'apartment' => $apartment,
            'advert' => $advert,
            'rentTypes' => $rentTypes,
            'image' => $image,
        ]);*/

        return $this->response($this->render('apartment_create.twig', [
            'isUpdate' => true,
            'city' => $city,
            'apartment' => $apartment,
            'advert' => $advert,
            'rentTypes' => $rentTypes,
            'image' => $image,
            // 'cities' => City::dropDownList(),
            'popularCities' => City::getPopular(),
            'metroWalks' => ApartmentModel::getMetroWalkArray(),
            'currencies' => ApartmentModel::getCurrencyArray(),
            'rooms' => ApartmentModel::getRoomsArray(),
            'sleepingPlaces' => ApartmentModel::getSleepingPlacesArray(),
            'beds' => ApartmentModel::getBedsArray(),
            'remont' => ApartmentModel::getRemontArray(),
            'safety' => ApartmentModel::getSafetyArray(),
            'heating' => ApartmentModel::getHeatingArray(),
            'floorCovering' => ApartmentModel::getFloorCoveringArray(),
            'bathroom' => ApartmentModel::getBathroomArray(),
            'buildingType' => ApartmentModel::getBuildingTypeArray(),
        ]));
    }

    /**
     * @param $id
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
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

        return $this->redirect(['/partners/office/apartments']);
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

}
