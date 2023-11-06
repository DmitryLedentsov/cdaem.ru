<?php

namespace common\modules\partners\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\web\HttpException;
use yii\widgets\ActiveForm;
use common\modules\users\models\Profile;
use common\modules\realty\models\RentType;
use common\modules\realty\models\Apartment;
use common\modules\partners\models\UserSeen;
use common\modules\partners\models\frontend\Advert;
use common\modules\partners\models\frontend as models;
use common\modules\partners\models\frontend\AdvertReservation;

/**
 * Заявки на резервацию
 * @package common\modules\partners\controllers\frontend
 */
class ReservationController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        $this->enableCsrfValidation = true;
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->module->viewPath = '@common/modules/partners/views/frontend';

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
                        'actions' => ['index', 'advert-reservation'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['reservations', 'total-bid', 'total-bid-want-rent', 'reservations-want-rent'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching/reservation.php'));
    }

    /**
     * Резервация апартаментов "Быстро снять"
     * @return Response
     * @throws HttpException
     */
    public function actionIndex() : Response
    {
        $model = new models\form\ReservationForm();

        if (Yii::$app->user->isGuest) {
            $model->scenario = 'unauthorized';
        }

        if ($model->load(Yii::$app->request->post())) {
            $errors = $this->validate($model);

            if (!$errors) {
                if ($model->process()) {
                    $msg = "
                    <p><strong>№ заявки: {$model->id}</strong></p>
                    <p>Заявка на бронирование успешно отправлена владельцу. Пожалуйста ожидайте подтверждения.</p>
                    <p style=\"padding:20px; font-size:20px; border:1px solid darkblue;\">Если вы еще не зарегистрированы, обязательно подтвердите регистрацию у себя на e-mail</p>
                    ";
                    Yii::$app->session->setFlash('success', $msg);
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');
                }

                return $this->refresh();
            }

            if (Yii::$app->request->isAjax) {
                return $this->validationErrorsAjaxResponse($errors);
            }
        }

        return $this->response($this->render('reservation.twig', [
            'reservationsForm' => $model,
            'rentTypes' => RentType::rentTypeslist(),
            'rooms' => Apartment::getRoomsArray(true),
            'floor' => Apartment::getFloorArray(),
            'sleepingPlaces' => Apartment::getSleepingPlacesArray(true),
            'metroWalkList' => Apartment::getMetroWalkArray(),
        ]));
    }

    /**
     * Создание заявки на бронь объявления
     * @param $advert_id
     * @return Response
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public function actionAdvertReservation($advert_id) : Response
    {
        $userUnconfirmedReservation = AdvertReservation::find()
            ->where(['confirm' => 2])->andWhere(['user_id' => Yii::$app->user->id])
            ->all();

        if (!empty($userUnconfirmedReservation)) {
            $errorMessage = 'У вас есть неподтвержденная бронь! Пожалуйста подтвердите свою заявку, или отмените прежде чем сделать новую';

            if (Yii::$app->request->isAjax) {
                return $this->validationTotalErrorAjaxResponse($errorMessage);
            }

            Yii::$app->session->setFlash('danger', $errorMessage);
            return $this->redirect(['/office/reservations']);
        }

        $advert = $this->findAdvert($advert_id);
        $otherAdvert = $this->findOtherAdvert($advert->apartment->user_id);
        $form = new models\form\AdvertReservationForm();
        if (Yii::$app->user->isGuest) {
            $form->scenario = 'unauthorized';
        }
        $form->advert_id = $advert_id;
        $form->landlord_id = $advert->apartment->user_id;

        if ($form->load(Yii::$app->request->post())) {
            $errors = $this->validate($form);

            if (!Yii::$app->user->isGuest && $form->landlord_id === Yii::$app->user->id) {
                return $this->validationTotalErrorAjaxResponse("Вы не можете оставить заявку на бронь к своим апартаментам.");
            }

            if (!$errors) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($form->process()) {
                        // Отправить уведомление
                        Yii::$app->consoleRunner->run('partners/reservation/send-mail ' . $form->id . ' reservation');

                        $msg = "<h4>№ заявки: {$form->id}</h4>

                             <p>Заявка на бронирование успешно отправлена владельцу. Пожалуйста ожидайте подтверждения.</p>
                             
                             <p style=\"padding:20px; font-size:20px; border:1px solid darkblue;\">
                             Если вы еще не зарегистрированы, обязательно подтвердите регистрацию у себя на e-mail</p>
                        ";
                        Yii::$app->session->setFlash('success', $msg);

                        if (Yii::$app->request->isAjax) {
                            return $this->successAjaxResponse(strip_tags($msg));
                        }
                    } else {
                        Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');
                        if (Yii::$app->request->isAjax) {
                            return $this->criticalErrorsAjaxResponse(new \Exception());
                        }

                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');
                    if (Yii::$app->request->isAjax) {
                        return $this->criticalErrorsAjaxResponse(new \Exception());
                    }

                }
                $transaction->commit();

                if (Yii::$app->request->isAjax) {
                    return $this->successAjaxResponse("                        
                        Заявка успешно отправлена, вы можете увидеть её
                        в личном кабинете. Далее, при подтверждении брони Владельцем и Вами, 
                        контакты обеих сторон станут доступными."
                    );
                }
                return $this->refresh();
            }

            if (Yii::$app->request->isAjax) {
                return $this->validationErrorsAjaxResponse($errors);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->criticalErrorsAjaxResponse(new \Exception());
        }

        return $this->response($this->render('_advert_reservation_form.twig', [
            'reservationsForm' => $form, 'otherAdvert' => $otherAdvert,
        ]));
    }

    /**
     * Общие заявки
     * @return Response
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionTotalBid() : Response
    {
        // Если тип пользователя "Хочу снять" выполняем специализированный экнш
        if (Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {
            return Yii::$app->runAction('/partners/reservation/total-bid-want-rent');
        }

        $allReservationsCount = models\Reservation::globalActiveCount(Yii::$app->request->cityId);
        $openReservationsCount = models\Reservation::globalOpenCount();

        $searchModel = new models\search\ReservationSearch(['scenario' => 'owner']);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query2 = clone $dataProvider->query;
        UserSeen::updateLastDate(models\search\ReservationSearch::tableName(), $query2->select('max(date_update)')->scalar(), 'owner' . Yii::$app->request->cityId);

        return $this->response($this->render('reservations-total-bid.twig', [
            'allReservationsCount' => $allReservationsCount,
            'openReservationsCount' => $openReservationsCount,
            'model' => $searchModel,
            'dataProvider' => $dataProvider,
        ]));
    }

    /**
     * Общие заявки пользователя с типом аккаунта want-rent
     * @return Response
     */
    public function actionTotalBidWantRent() : Response
    {
        $searchModel = new models\search\ReservationSearch(['scenario' => 'global']);
        $dataProvider = $searchModel->wantRentSearch(Yii::$app->request->queryParams);
        $dataProvider->pagination->route = '/partners/reservation/total-bid';

        $query2 = clone $dataProvider->query;
        UserSeen::updateLastDate(models\search\ReservationSearch::tableName(), $query2->select('max(date_update)')->scalar(), 'renter');

        return $this->response($this->render('reservations-want-rent-total-bid', [
            'dataProvider' => $dataProvider,
        ]));
    }

    /**
     * Просмотр арендодателем заявок на бронь его объявлений
     * @return string
     */
    public function actionReservations() : Response
    {
        // Если тип пользователя "Хочу снять" выполняем специализированный экнш
        if (Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {
            return Yii::$app->runAction('/partners/reservation/reservations-want-rent');
        }

        $searchModel = new models\search\AdvertReservationSearch(['scenario' => 'landlord']);
        $dataProvider = $searchModel->landlordSearch();
        $otherReservation = new models\AdvertReservation;

        $query2 = clone $dataProvider->query;
        UserSeen::updateLastDate(models\search\AdvertReservationSearch::tableName(), $query2->select('max(date_update)')->scalar(), 'landlord');

        return $this->response($this->render('reservations.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'otherReservation' => $otherReservation,
        ]));
    }

    /**
     * Просмотр арендатором (создателем) заявок на бронь объявлений
     * @return string
     */
    public function actionReservationsWantRent() : Response
    {
        $searchModel = new models\search\AdvertReservationSearch(['scenario' => 'renter']);
        $dataProvider = $searchModel->renterSearch();
        $dataProvider->pagination->route = '/partners/reservation/reservations';
        $userUnconfirmedReservation = AdvertReservation::find()
            ->where(['confirm' => 2])->andWhere(['user_id' => Yii::$app->user->id])
            ->all();
        // todo вернуть после отладки фомры бронирования, иначе нужн обудет удалять бронь после каждого создания
        //   if (empty($userUnconfirmedReservation)){
        //       Yii::$app->session->setFlash('danger', 'У вас есть не подтвержденная бронь!');
        //   }
        $query2 = clone $dataProvider->query;
        $tableName = models\search\AdvertReservationSearch::tableName();
        UserSeen::updateLastDate($tableName, $query2->select('max(' . $tableName . '.date_update)')->scalar(), 'renter');

        return $this->response($this->render('reservations-want-rent.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userUnconfirmedReservation' => $userUnconfirmedReservation,
        ]));
    }

    /**
     * Находит ApartmentAdverts model по полю advert_id
     * Незабаненного пользователя
     * @param $advert_id
     * @param null $city_Name_Eng
     * @return yii\db\ActiveRecord
     * @throws HttpException
     */
    protected function findAdvert($advert_id, $city_name_eng = null) : Advert
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

    /**
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function findOtherAdvert($userId)
    {
        return Advert::getAdvertsByUser($userId, 10);
    }
}
