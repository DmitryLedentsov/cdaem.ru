<?php

namespace common\modules\partners\controllers\frontend;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\ForbiddenHttpException;
use common\modules\partners\models\Advert;
use common\modules\realty\models\RentType;
use common\modules\partners\models\Service;
use common\modules\partners\models\frontend as models;
use common\modules\partners\models\frontend\Apartment;
use common\modules\partners\models\frontend\form\ReservationFailureForm;
use common\modules\partners\models\frontend\form\AdvertReservationConfirmForm;

/**
 * Class AjaxController
 * @package common\modules\partners\controllers\frontend
 */
class AjaxController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
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

        $this->module->viewPath = '@common/modules/partners/views/frontend';

        if (!Yii::$app->request->isAjax) {
            return $this->goHome();
        }

        return true;
    }

    /**
     * Незаезд
     * @param $id
     * @return array|mixed|string
     * @throws \Exception
     */
    public function actionReservationFailure($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $failure = new ReservationFailureForm(['reservation_id' => $id]);
            $failure->load(Yii::$app->request->post());

            if ($failure->create()) {
                // Отправить уведомление
                Yii::$app->consoleRunner->run('partners/reservation/send-mail-nezaezd ' . $id);

                return [
                    'status' => 1,
                    'message' => 'Данные успешно сохранены',
                ];
            } else {
                if ($failure->hasErrors()) {
                    return current($failure->getErrors());
                }

                return [
                    'status' => 0,
                    'message' => 'Возникла критическая ошибка',
                ];
            }
        } else {
            Yii::$app->response->format = Response::FORMAT_HTML;

            return $this->renderAjax('reservation-failure.php', [
                'reservationId' => $id
            ]);
        }
    }

    /**
     * Возвращает данные для подтверждения или отмена заявки
     * @return array|string|string[]
     */
    public function actionReservationConfirm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $action = trim(Yii::$app->request->post('action', 'confirm'));
            $type = trim(Yii::$app->request->post('type', 'cancel'));
            $reservationId = trim(Yii::$app->request->post('reservation_id'));
            $department = trim(Yii::$app->request->post('department'));
            $userType = trim(Yii::$app->request->post('user_type'));
            $user_id = trim(Yii::$app->request->post('user_id'));
            $priced = trim(Yii::$app->request->post('priced'));

            if ($action == 'send') {
                if ($type == 'confirm') {
                    $reservation = null;
                    $query = AdvertReservationConfirmForm::find()
                        ->closed(0)
                        ->cancel(0)
                        ->andWhere(['id' => $reservationId]);

                    if ($userType == 'renter') {
                        $query->thisUser();
                    }

                    if ($userType == 'landlord') {
                        $query->thisLandlord();
                    }

                    $reservation = $query->one();

                    if (!$reservation) {
                        $transaction->commit();

                        return [
                            'status' => 0,
                            'message' => 'Возникла критическая ошибка',
                        ];
                    }

                    if ($user_id != 0) {
                        return [
                            'status' => 0,
                            'message' => 'Другой Владелец подтвердил бронь Вашего Клиента быстрее. Заявка аннулирована !',
                        ];
                    }

                    $reservation->scenario = 'confirm';
                    if ($reservation->confirm()) {
                        $transaction->commit();
                        if ($user_id != 0) {
                            return [
                                'status' => 0,
                                'message' => 'У арендатора есть неподтвержденная бронь, в целях безопасности дождитесь подтверждения со стороны клиента!!!',
                            ];
                        }
                        // Отправить уведомление
                        Yii::$app->consoleRunner->run('partners/reservation/send-mail ' . $reservation->id . ' confirm');

                        return [
                            'status' => 1,
                            'message' => 'С вашего счёта списано ' . $priced . '. Вы забронировали, Владелец вас ждёт! Вам открыты все контакты и функция переписки!',
                        ];
                    }

                    $transaction->rollBack();

                    return [
                        'status' => 0,
                        'message' => current($reservation->getErrors()),
                    ];
                } else {

                    // ОТМЕНА глобальной резервации. Возможна только WANT_RENT'ом
                    if ($department == 'total-bid' and $userType == 'renter') {
                        $reservation = models\Reservation::find()
                            ->thisUser()
                            ->cancel(0)
                            ->andWhere(['id' => $reservationId])
                            ->one();

                        if (!$reservation) {
                            return [
                                'status' => 0,
                                'message' => 'Возникла критическая ошибка',
                            ];
                        }

                        $reservation->cancel_reason = Yii::$app->request->post('cancel_reason');

                        // Проверка на стринг
                        // if (!is_string($reservation->cancel_reason)) {
                        //     return [
                        //        'status' => 0,
                        //        'message' => 'Возникла критическая ошибка',
                        //      ];
                        //  }
                        // Проверка на мин макс
                        //  $mbStrlen = mb_strlen($reservation->cancel_reason);
                        //  if ($mbStrlen < 10) {
                        //      return ['Причина отмены должно содержать минимум 10 символов'];
                        //   }
                        //   if ($mbStrlen > 255) {
                        //        return ['Причина отмены должно содержать максимум 255 символов'];
                        //     }

                        $reservation->cancel = 1;
                        $reservation->closed = 1;
                        $reservation->date_update = date('Y-m-d H:i:s');
                        $reservation->save(false);

                        $transaction->commit();

                        return [
                            'status' => 1,
                            'message' => 'Данные успешно сохранены',
                        ];
                    }

                    $reservation = null;
                    if ($department == 'reservations') {
                        $query = AdvertReservationConfirmForm::find()->where(['id' => $reservationId])->cancel(0);
                        if ($userType == 'renter') {
                            $reservation = $query->thisUser()->one();
                        }
                        if ($userType == 'landlord') {
                            $reservation = $query->thisLandlord()->one();
                        }
                    }

                    if (!$reservation) {
                        return [
                            'status' => 0,
                            'message' => 'Возникла критическая ошибка',
                        ];
                    }

                    $reservation->scenario = 'cancel';
                    $reservation->load(Yii::$app->request->post());

                    if ($reservation->cancel()) {
                        $transaction->commit();

                        // Только в бронях к адвертам при отмене нужно когото уведомлять.
                        if ($department == 'reservations') {
                            // Отправить уведомление
                            Yii::$app->consoleRunner->run('partners/reservation/send-mail ' . $reservation->id . ' cancel');
                        }

                        return [
                            'status' => 1,
                            'message' => 'Данные успешно сохранены',
                        ];
                    }

                    $transaction->rollBack();

                    return [
                        $reservation->getFirstError('cancel_reason')
                    ];
                }
            } else {
                $transaction->commit();

                $advertPrice = models\AdvertReservation::find()
                    ->select('price')->joinWith('advert')
                    ->where([models\AdvertReservation::tableName() . '.id' => $reservationId])->scalar();

                return $this->renderAjax('reservation-confirm.php', [
                    'type' => $type,
                    'reservationId' => $reservationId,
                    'department' => $department,
                    'advertPrice' => $advertPrice,
                    'userType' => $userType,
                    'user_id' => $user_id,
                    'priced' => $priced,

                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ['Возникла ошибка, пожалуйста попробуйте еще раз или обратитесь в службу технической поддержки.'];
        }
    }

    /**
     * Возвращает данные объектов недвижимости для сервисов
     * @return string
     * @throws \Exception
     */
    public function actionRealtyObjectsByService()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        $service = trim(Yii::$app->request->get('service'));
        $apartmentId = trim(Yii::$app->request->get('apartment_id'));
        $advertisementId = trim(Yii::$app->request->get('advertisement_id'));
        $rentTypeslist = RentType::rentTypeslist();

        return $this->renderAjax('realty-objects-by-service.php', [
            'service' => $service,
            'apartmentId' => $apartmentId,
            'rentTypeslist' => $rentTypeslist,
            'advertisementId' => $advertisementId
        ]);
    }

    /**
     * Возвращает данные для покупки сервиса
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBuyService()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            Yii::$app->response->format = Response::FORMAT_HTML;

            // Калькулятор стоимости выбранных бъектов
            if (Yii::$app->request->post('request') == 'calc') {
                // Инициализация сервиса
                $service = Yii::$app->service->load(Yii::$app->request->post('service'));

                // Расчитываем стоимость сервиса
                $model = new Service();
                $calculation = $model->calculation($service, [
                    'selected' => Yii::$app->request->post('selected'),
                    'days' => Yii::$app->request->post('days'),
                ]);

                $date = Yii::$app->request->post('date');
                $currentDate = date('d.m.Y');
                $validator = new \yii\validators\DateValidator();
                $validator->format = 'php:d.m.Y';

                if (!$validator->validate($date) || strtotime($currentDate) > strtotime($date)) {
                    $date = $currentDate;
                }

                $advertisementId = Yii::$app->request->post('advertisementId');

                Yii::$app->session->set('buy-service', [
                    'service' => Yii::$app->request->post('service'),
                    'selected' => Yii::$app->request->post('selected'),
                    'calculation' => $calculation,
                    'date' => $date,
                    'advertisementId' => $advertisementId
                ]);

                $transaction->commit();

                return $this->renderAjax('calculation-service.php', [
                    'service' => $service,
                    'calculation' => $calculation,
                    'date' => $date,
                ]);
            } // Оплата выбранных объектов
            else {
                $data = Yii::$app->session->get('buy-service');

                if (empty($data)) {
                    throw new \Exception('No session data');
                }

                // Инициализация сервиса
                $service = Yii::$app->service->load($data['service']);

                // dd($data);

                $days = $service->isTimeInterval() ? $data['calculation']['days'] : null;

                // Добавить процесс в обработку
                $processId = Service::addProcess($data['service'], $data['date'], $days, [
                    'selected' => $data['selected'],
                    'days' => $days,
                    'discount' => $data['calculation']['discount'],
                    'price' => Yii::$app->user->can('admin') ? 1 : $data['calculation']['price'],
                    'advertisementId' => $data['advertisementId']
                ]);

                if (!$processId) {
                    throw new \Exception('Process save failed');
                }

                $transaction->commit();
                Yii::$app->session->remove('buy-service');

                return $this->renderAjax('buy-service.php', [
                    'service' => $service,
                    'selected' => $data['selected'],
                    'calculation' => $data['calculation'],
                    'processId' => $processId,
                    'date' => $data['date'],
                ]);
            }
        } catch (\Exception $e) {
            Yii::$app->session->remove('buy-service');
            $transaction->rollBack();

            // return Html::tag('div', 'Возникла ошибка, пожалуйста попробуйте еще раз или обратитесь в службу технической поддержки.', ['class' => 'alert alert-danger']);
            return Html::tag('div', $e->getMessage(), ['class' => 'alert alert-danger']);
        }
    }

    /**
     * Управление изображениями
     * @param $action
     * @param $id
     * @return array|Response
     */
    public function actionControlImage($action, $id)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $image = models\Image::find()
                ->joinWith([
                    'apartment' => function ($query) {
                        $query->where('user_id = :user_id', [':user_id' => Yii::$app->user->id]);
                    }
                ])
                ->where('image_id = :image_id', [':image_id' => $id])
                ->one();

            // Удалить изображение
            if ($action == 'delete') {
                if ($image) {
                    $image->apartment->date_update = date('Y-m-d H:i:s');
                    $image->apartment->save(false);
                    $image->deleteWithFiles();
                    $transaction->commit();

                    return [
                        'status' => 1
                    ];
                }
            } // Сделать изображение главным
            elseif ($action == 'index') {
                if ($image) {
                    $connection = Yii::$app->db;
                    $connection->createCommand()
                        ->update(models\Image::tableName(), ['default_img' => 0], ['apartment_id' => $image->apartment_id])
                        ->execute();
                    $image->default_img = 1;
                    $image->apartment->date_update = date('Y-m-d H:i:s');
                    $image->apartment->save(false);
                    $image->save(false);
                    $transaction->commit();

                    return [
                        'status' => 1
                    ];
                }
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            return [
                'status' => 0
            ];
        }

        return [
            'status' => 0
        ];
    }

    /**
     * Календарь
     * @return array
     */
    public function actionCalendar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = Yii::$app->request->get('start', null);
        $end = Yii::$app->request->get('end', null);
        $models = models\Calendar::findRecordsByDateInterval($start, $end);
        $result = [];

        foreach ($models as $model) {
            $address = $model->apartment->address;
            $color = '#37BF7F';
            if ($model->reserved) {
                $address = $model->apartment->address;
                $color = '#ff9f89';
            }
            $result[] = [
                'title' => date('H:i', strtotime($model->date_from)) . ' - ' . date('H:i', strtotime($model->date_to)),
                'start' => $model->date_from,
                'end' => $model->date_to,
                'color' => $color,
                'address' => $address,
            ];
        }

        return $result;
    }

    /**
     * Возвращает данные объектов недвижимости для календаря
     * @return array|bool|string
     */
    public function actionCalendarSelected()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model = new models\form\CalenderForm();
                $model->load(Yii::$app->request->post());
                $errors = ActiveForm::validate($model);

                if (!$errors) {
                    $model->process();
                    $transaction->commit();
                    if ($model->hasManualErrors) {
                        return $model->manualErrors;
                    }

                    return [
                        'status' => 1,
                        'message' => 'Данные успешно сохранены'
                    ];
                }

                $transaction->commit();

                return $errors;
            } catch (\Exception $e) {
                Yii::$app->session->remove('buy-service');
                $transaction->rollBack();

                return [
                    'status' => 0,
                    'message' => 'Возникла ошибка, пожалуйста попробуйте еще раз или обратитесь в службу технической поддержки.'
                ];
            }
        } else {
            Yii::$app->response->format = Response::FORMAT_HTML;
            $model = new models\form\CalenderForm();
            $date = $model->getDate(trim(Yii::$app->request->get('date', null)));
            if (Yii::$app->request->get('type') == 'info') {
                $calendar = models\Calendar::findRecordsByDate($date);

                return $this->renderAjax('calendar/objects-by-date.php', [
                    'date' => $date,
                    'calendar' => $calendar,
                ]);
            }

            if (Yii::$app->request->get('type') == 'selected') {
                $apartments = models\Apartment::findApartmentsByUser(Yii::$app->user->id);

                return $this->renderAjax('calendar/objects-by-apartments.php', [
                    'model' => $model,
                    'apartments' => $apartments,
                    'date' => $date,
                ]);
            }

            return $this->renderAjax('calendar/realty-objects-by-calendar.php', [
                'date' => $date,
            ]);
        }
    }

    /**
     * Обновляем цены объявления по типу аренды
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdatePrice()
    {
        // if (!Yii::$app->user->can('agency-advert-view')) { // TODO небоходимо проверять другое право (partners)
        //     throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        // }

        $paramList = Yii::$app->request->getBodyParams();

        $advertId = $paramList['advert-id'];
        $rentPrice = $paramList['rent-price'];
        $isApplyForAll = isset($paramList['apply-for-all']) ? true : false;

        $advert = Advert::findOne($advertId);

        if (!$advert) {
            \Yii::$app->response->statusCode = 404;

            return $this->response(Json::encode(['message' => "Не найдено объявление с идентификатором {$advertId}"]));
        }

        /** @var Apartment $apartment */
        $apartment = Apartment::findOne($advert->apartment_id);

        if (!$apartment) {
            \Yii::$app->response->statusCode = 404;

            return $this->response(Json::encode(['message' => "Не найдены апартаменты с идентификатором {$advert->apartment_id}"]));
        }

        if (Yii::$app->user->id !== $apartment->user_id) {
            \Yii::$app->response->statusCode = 404;

            return $this->response(Json::encode(['message' => 'У вас нет прав на редактирование данного объявления']));
        }

        $advert->price = $rentPrice;
        $advert->save(false);

        if ($isApplyForAll) {
            // Применяем цену для всех типов аренды
            $advertList = Advert::findAll(['apartment_id' => $advert->apartment_id]);

            foreach ($advertList as $advert) {
                $advert->price = $rentPrice;
                $advert->save(false);
            }
        }

        $this->redirect('/office/apartments');
    }
}
