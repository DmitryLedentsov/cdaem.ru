<?php

namespace frontend\modules\partners\controllers;

use Yii;
use yii\web\Response;
use yii\web\HttpException;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\geo\models\City;
use common\modules\users\models\Profile;
use common\modules\partners\models\UserSeen;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models as models;
use frontend\modules\partners\models\AdvertReservation;

/**
 * Заявки на резервацию
 * @package frontend\modules\partners\controllers
 */
class ReservationController extends \frontend\components\Controller
{
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
                        'actions' => ['index', 'advert-reservation'],
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                    [
                        'actions' => ['reservations', 'total-bid', 'total-bid-want-rent', 'reservations-want-rent'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../caching/reservation.php'));
    }

    /**
     * Резервация апартаментов "Быстро снять"
     * @return array|string|Response
     * @throws HttpException
     */
    public function actionIndex()
    {
        $model = new models\form\ReservationForm();

        if (Yii::$app->user->isGuest) {
            $model->scenario = 'unauthorized';
        }

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (!$errors) {
                if ($model->process()) {
                    $msg = '<p><strong>№ заявки: ' . $model->id . '</strong></p>';
                    $msg .= '<p>Заявка на бронирование успешно отправлена владельцу. Пожалуйста ожидайте подтверждения.</p>';
                    $msg .= '<p style="padding:20px; font-size:20px; border:1px solid darkblue;">Если вы еще не зарегистрированы, обязательно подтвердите регистрацию у себя на e-mail</p>';
                    Yii::$app->session->setFlash('success', $msg);
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');
                }

                return $this->refresh();
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
        }

        return $this->render('_form.twig', [
            'reservationsForm' => $model,
        ]);
    }

    /**
     * Создание заявки на бронь объявления
     * @param $advert_id
     * @return array|string|Response
     * @throws HttpException
     */
    public function actionAdvertReservation($advert_id)
    {
        $reservationtakewidth = AdvertReservation::find()
            ->where(['confirm' => 2])->andWhere(['user_id' => Yii::$app->user->id])
            ->all();
        if (!empty($reservationtakewidth)) {
            Yii::$app->session->setFlash('danger', 'У вас есть неподтвержденная бронь! Пожалуйста подтвердите свою заявку, или отмените прежде чем сделать новую ');

            return $this->redirect(['/office/reservations']);
        }

        $advert = $this->findAdvert($advert_id);
        $otheradvert = $this->findOtherAdvert($advert->apartment->user_id);
        $form = new models\form\AdvertReservationForm();
        if (Yii::$app->user->isGuest) {
            $form->scenario = 'unauthorized';
        }
        $form->advert_id = $advert_id;
        $form->landlord_id = $advert->apartment->user_id;
        if ($form->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($form);
            if (!$errors) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($form->process()) {

                        // Отправить уведомление
                        Yii::$app->consoleRunner->run('partners/reservation/send-mail ' . $form->id . ' reservation');

                        $msg = '<h4>№ заявки: ' . $form->id . '</h4>';
                        $msg .= '<p>Заявка на бронирование успешно отправлена владельцу. Пожалуйста ожидайте подтверждения.</p>';
                        $msg .= '<p style="padding:20px; font-size:20px; border:1px solid darkblue;">Если вы еще не зарегистрированы, обязательно подтвердите регистрацию у себя на e-mail</p>';
                        Yii::$app->session->setFlash('success', $msg);
                    } else {
                        Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');
                }
                $transaction->commit();

                return $this->refresh();
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
        }

        return $this->render('_advert_reservation_form.twig', [
            'reservationsForm' => $form, 'otheradvert' => $otheradvert,
        ]);
    }

    /**
     * Общие заявки
     * @return int|mixed|string
     * @throws HttpException
     */
    public function actionTotalBid()
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

        return $this->render('reservations-total-bid.twig', [
            'allReservationsCount' => $allReservationsCount,
            'openReservationsCount' => $openReservationsCount,
            'model' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Общие заявки пользователя с типом аккаунта want-rent
     * @return string
     */
    public function actionTotalBidWantRent()
    {
        $searchModel = new models\search\ReservationSearch(['scenario' => 'global']);
        $dataProvider = $searchModel->wantRentSearch(Yii::$app->request->queryParams);
        $dataProvider->pagination->route = '/partners/reservation/total-bid';

        $query2 = clone $dataProvider->query;
        UserSeen::updateLastDate(models\search\ReservationSearch::tableName(), $query2->select('max(date_update)')->scalar(), 'renter');


        return $this->render('reservations-want-rent-total-bid', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр арендодателем заявок на бронь его объявлений
     * @return string
     */
    public function actionReservations()
    {
        // Если тип пользователя "Хочу снять" выполняем специализированный экнш
        if (Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {
            return Yii::$app->runAction('/partners/reservation/reservations-want-rent');
        }

        $searchModel = new models\search\AdvertReservationSearch(['scenario' => 'landlord']);

        $dataProvider = $searchModel->landlordSearch();


        $otherreservation = new models\AdvertReservation;


        $query2 = clone $dataProvider->query;
        UserSeen::updateLastDate(models\search\AdvertReservationSearch::tableName(), $query2->select('max(date_update)')->scalar(), 'landlord');

        return $this->render('reservations.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'otherreservation' => $otherreservation,

        ]);
    }

    /**
     * Просмотр арендатором (создателем) заявок на бронь объявлений
     * @return string
     */
    public function actionReservationsWantRent()
    {
        $searchModel = new models\search\AdvertReservationSearch(['scenario' => 'renter']);
        $dataProvider = $searchModel->renterSearch();
        $dataProvider->pagination->route = '/partners/reservation/reservations';
        $reservationtakewidth = AdvertReservation::find()
            ->where(['confirm' => 2])->andWhere(['user_id' => Yii::$app->user->id])
            ->all();
        //   if (empty($reservationtakewidth)){
        //       Yii::$app->session->setFlash('danger', 'У вас есть не подтвержденная бронь!');
        //   }
        $query2 = clone $dataProvider->query;
        $tableName = models\search\AdvertReservationSearch::tableName();
        UserSeen::updateLastDate($tableName, $query2->select('max(' . $tableName . '.date_update)')->scalar(), 'renter');

        return $this->render('reservations-want-rent.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'reservationtakewidth' => $reservationtakewidth,
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

    protected function findOtherAdvert($user_id)
    {
        $userAdverts = Advert::getAdvertsByUser($user_id, 10);

        return $userAdverts;
        //findApartmentsByAvailable($userId = null)
    }
}
