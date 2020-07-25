<?php

namespace common\modules\partners\commands;

use common\modules\partners\models\AdvertReservation;
use common\modules\partners\models\ReservationFailure;
use yii\helpers\Console;
use Yii;

/**
 * Reservation
 * @package common\modules\partners\commands
 */
class ReservationController extends \yii\console\Controller
{
    /**
     * Send email By ReservationId
     *
     * @param $reservationId
     * @return bool|int
     */
    public function actionSendMail($reservationId, $type)
    {
        try {
            $reservation = AdvertReservation::findOne($reservationId);

            if (!$reservation) {
                throw new \Exception('Заявки под номером ' . $reservationId . ' не существует в базе данных.');
            }

            // Если пользователь отправил заявку на резервацию апаратаментов
            if ($type == 'reservation') {

                // Отправить EMAIL
                $this->sendEmail(
                    'partners-reservation',
                    $subject = 'Вам поступила новая заявка на бронь №' . $reservation->id . ' - ' . Yii::$app->params['siteDomain'],
                    $reservation->landlord->email,
                    [
                        'reservation' => $reservation
                    ]
                );

                // Отправить sms
                $this->sendSms(
                    $reservation,
                    $type,
                    $reservation->landlord->phone,
                    'Вам поступила Бронь (№ ' . $reservation->id . '), подтвердите!'
                );

            } else {

                // Если оба пользователя подтвердили заявку
                // Необходимо отправить два письма с разными данными
                if ($reservation->confirm == AdvertReservation::BOTH) {

                    $subject = 'Заявка на бронь №' . $reservation->id . ' успешно подтверждена обеими сторонами на сайте - ' . Yii::$app->params['siteDomain'];

                    // Отправить EMAIL
                    $this->sendEmail(
                        'partners-reservation-deal',
                        $subject,
                        $reservation->user->email,
                        [
                            'userType' => 'client',
                            'reservation' => $reservation
                        ]
                    );

                    // Отправить EMAIL
                    $this->sendEmail(
                        'partners-reservation-deal',
                        $subject,
                        $reservation->landlord->email,
                        [
                            'userType' => 'owner',
                            'reservation' => $reservation
                        ]
                    );

                    // Отправить sms
                    $this->sendSms(
                        $reservation,
                        $type,
                        $reservation->user->phone, 'Заявка на бронь (№ ' . $reservation->id . ') подтверждена обеими сторонами'
                    );
                    $this->sendSms(
                        $reservation,
                        $type,
                        $reservation->landlord->phone,
                        'Заявка на бронь (№ ' . $reservation->id . ') подтверждена обеими сторонами'
                    );
                }

                // Отправка письма клиенту или владельцу с информацией
                // об отмене или подтверждению заявки
                else {
                    $emailData = $this->getEmailData($reservation, $type);
                    // Отправить EMAIL
                    $this->sendEmail(
                        $emailData['template'],
                        $emailData['subject'],
                        $emailData['user']->email,
                        [
                            'type' => $type,
                            'userType' => $emailData['userType'],
                            'reservation' => $reservation
                        ]
                    );

                    // Отправить sms
                    $smsMessage = $emailData['userTypeText'] . ' ' . $emailData['typeText'] . ' заявку на бронь (№ ' . $reservation->id . ').';
                    $smsMessage .= ($type == 'confirm') ? ' Подтвердите и Вы!' : '';
                    $this->sendSms(
                        $reservation,
                        $type,
                        $emailData['user']->phone,
                        $smsMessage
                    );
                }

            }

            $this->stdout('SUCCESS' . PHP_EOL, Console::FG_GREEN);

        } catch (\Exception $e) {
            $this->setErrorLogReport($e, 'reservation-email', 'Заявка на бронь ID' . $reservationId);
            $this->stdout('FAIL: process ID' . $reservationId . ' ' . $e->getMessage() . PHP_EOL, Console::FG_RED);
        }
    }


    /**
     * Send email nezaezd By ReservationId
     *
     * @param $reservationId
     */
    public function actionSendMailNezaezd($reservationId)
    {
        try {
            $reservation = AdvertReservation::findOne($reservationId);

            if (!$reservation) {
                throw new \Exception('Заявки под номером ' . $reservationId . ' не существует в базе данных.');
            }

            $reservationFailure = ReservationFailure::find()
                ->where(['reservation_id' => $reservation->id])
                ->orderBy('id DESC')
                ->one();

            // Кто создал заявку
            if ($reservation->failure->user_id == $reservation->user_id) {
                $userType = 'renter';
                $secondSideUser = $reservation->landlord;
                $secondSideText = 'Владелец';
                $firstSideText = 'Клиент';
            } else {
                $userType = 'landlord';
                $secondSideUser = $reservation->user;
                $secondSideText = 'Клиент';
                $firstSideText = 'Владелец';
            }


            // Письмо создателю незаезда
            if ($reservationFailure->conflict != 1) {
                // Отправить EMAIL
                $this->sendEmail(
                    'partners-reservation-nezaezd-creator',
                    'Указан "Незаезд" на сайте - ' . Yii::$app->params['siteDomain'],
                    $reservation->failure->user->email,
                    [
                        'id' => $reservation->failure->id,
                        'title' => 'Заявка "Незаезд" №' . $reservation->failure->id . ' успешно создана.',
                        'userType' => $userType,
                        'secondSide' => $secondSideText
                    ]
                );
            }

            // Письмо второй стороне незаезда
            if ($reservationFailure->conflict != 1) {
                // Отправить EMAIL
                $this->sendEmail(
                    'partners-reservation-nezaezd-2side',
                    'Указан "Незаезд" на сайте - ' . Yii::$app->params['siteDomain'],
                    $secondSideUser->email,
                    [
                        'id' => $reservation->failure->id,
                        'title' => 'Создана заявка "Незаезд" №' . $reservation->failure->id . '. Причина: ' . $reservation->failure->cause_text,
                        'userType' => $userType
                    ]
                );

                $this->sendSms(
                    $reservation,
                    'nezaezd-2side',
                    $secondSideUser->phone,
                    $firstSideText . ' указал "Незаезд" для заявки (№ ' . $reservation->id . ')'
                );
            }

            // Отправить EMAIL админу
            $this->sendEmail(
                'partners-reservation-nezaezd-admin',
                'Указан "Незаезд" на сайте - ' . Yii::$app->params['siteDomain'],
                Yii::$app->params['adminEmail'],
                [
                    'id' => $reservationFailure->id,
                    'reservationId' => $reservationId,
                    'title' => 'Создана заявка "Незаезд" №' . $reservationFailure->id . '. Причина: ' . $reservationFailure->cause_text,
                    'userType' => $userType
                ]
            );

            Yii::info('Незаезд: Заявка на бронь ID' . $reservationId . ' - Успешно', 'reservation-email');
            $this->stdout('SUCCESS' . PHP_EOL, Console::FG_GREEN);

        } catch (\Exception $e) {
            $this->setErrorLogReport($e, 'reservation-email', 'Незаезд: Заявка на бронь ID' . $reservationId);
            $this->stdout('FAIL: process ID' . $reservationId . ' ' . $e->getMessage() . PHP_EOL, Console::FG_RED);
        }
    }


    /**
     * Отправка сообщения после возврата средств по заявке "Незаезд"
     *
     * @param $failureId
     * @param $funds
     */
    public function actionSendMailFailureProcessed($failureId, $funds)
    {
        try {
            $failure = ReservationFailure::findOne($failureId);

            if (!$failure) {
                throw new \Exception('Заявки "Незаезд" под номером ' . $failureId . ' не существует в базе данных.');
            }

            // Отправить EMAIL
            $this->sendEmail(
                'partners-reservation-failure-processed',
                'Возврат средств по условию "Незаезд" на сайте - ' . Yii::$app->params['siteDomain'],
                $failure->user->email,
                [
                    'failure' => $failure,
                    'funds' => $funds,
                ]
            );

            // Отправить SMS
            $this->sendSms(
                $failure->reservation,
                'nezaezd-processed',
                $failure->user->phone,
                'Возврат средств по условию "Незаезд"'
            );

            $this->stdout('SUCCESS' . PHP_EOL, Console::FG_GREEN);

        } catch (\Exception $e) {
            $this->setErrorLogReport($e, 'reservation-email', 'Незаезд: Заявка ID' . $failureId);
            $this->stdout('FAIL: process ID' . $failureId . ' ' . $e->getMessage() . PHP_EOL, Console::FG_RED);
        }
    }


    /**
     * Send email nezaezd By ReservationId
     *
     * @param $reservationId
     * @param $userType
     * @param $funds
     */
    public function actionSendMailReturnByNotConfirm($reservationId, $userType, $funds)
    {
        try {
            $reservation = AdvertReservation::findOne($reservationId);

            if (!$reservation) {
                throw new \Exception('Заявки под номером ' . $reservationId . ' не существует в базе данных.');
            }

            $template = 'partners-reservation-not-confirm';
            $subject = 'Возврат средств по заявке на бронь №' . $reservation->id . ' на сайте - ' . Yii::$app->params['siteDomain'];

            if ($userType == 'renter') {
                // Отправить EMAIL
                $this->sendEmail(
                    $template,
                    $subject,
                    $reservation->user->email,
                    [
                        'user' => $reservation->user,
                        'userType' => 'renter',
                        'funds' => $funds,
                        'id' => $reservation->id
                    ]
                );

                // Отправить SMS
                $this->sendSms(
                    $reservation,
                    'nezaezd-2side',
                    $reservation->user->phone,
                    'Возврат средств. Владелец не подтвердил заявку на бронь (№ ' . $reservation->id . ')'
                );

            } else {
                // Отправить EMAIL
                $this->sendEmail(
                    $template,
                    $subject,
                    $reservation->landlord->email,
                    [
                        'user' => $reservation->user,
                        'userType' => 'landlord',
                        'funds' => $funds,
                        'id' => $reservation->id
                    ]
                );

                // Отправить SMS
                $this->sendSms(
                    $reservation,
                    'nezaezd-2side',
                    $reservation->landlord->phone,
                    'Возврат средств. Клиент не подтвердил заявку на бронь (№ ' . $reservation->id . ')'
                );
            }

            $this->stdout('SUCCESS' . PHP_EOL, Console::FG_GREEN);

        } catch (\Exception $e) {
            $this->setErrorLogReport($e, 'reservation-email', 'Возврат средств по заявке на бронь ID' . $reservationId);
            $this->stdout('FAIL: process ID' . $reservationId . ' ' . $e->getMessage() . PHP_EOL, Console::FG_RED);
        }
    }


    /**
     * Получить данные для отправки на почту
     * @param AdvertReservation $reservation
     * @param $type
     * @return array
     */
    private function getEmailData(AdvertReservation $reservation, $type)
    {
        $userType = null;

        if ($type == 'confirm') {
            $template = 'partners-reservation-confirm';
            if ($reservation->confirm == AdvertReservation::RENTER) {
                $userType = 'client';
                $user = $reservation->landlord;
            } else {
                $userType = 'owner';
                $user = $reservation->user;
            }
        } else {
            $template = 'partners-reservation-cancel';
            if ($reservation->cancel == AdvertReservation::RENTER) {
                $userType = 'client';
                $user = $reservation->landlord;
            } else {
                $userType = 'owner';
                $user = $reservation->user;
            }
        }

        $typeText = ($type == 'confirm') ? 'подтвердил' : 'отменил';
        $userTypeText = ($userType == 'owner') ? 'Владелец' : 'Клиент';
        $subject = $userTypeText . ' ' . $typeText . ' заявку на бронь №' . $reservation->id . ' на сайте - ' . Yii::$app->params['siteDomain'];

        return [
            'subject' => $subject,
            'userType' => $userType,
            'user' => $user,
            'template' => $template,
            'typeText' => $typeText,
            'userTypeText' => $userTypeText,
        ];
    }


    /**
     * @param $e
     * @param $cat
     * @param null $message
     */
    private function setErrorLogReport($e, $cat, $message = null)
    {
        $log = $message;
        $log .= PHP_EOL;
        $log .= 'Message: ' . $e->getMessage();
        $log .= PHP_EOL;
        $log .= 'File: ' . $e->getFile() . ':' . $e->getLine();
        Yii::error($log, $cat);
    }


    /**
     * Отправить письмо
     *
     * @param $template
     * @param $subject
     * @param $setTo
     * @param array $data
     * @return bool
     */
    private function sendEmail($template, $subject, $setTo, array $data)
    {
        $mail = Yii::$app->getMailer();
        $mail->viewPath = '@common/mails/reservation';

        $send = $mail->compose($template, $data)
            ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
            ->setTo($setTo)
            ->setSubject($subject)
            ->send();

        if ($send) {
            Yii::info($subject . ' - Отправлено', 'reservation-email');
        } else {
            Yii::warning($subject . ' - Не удалось отправить. Email: ' . $setTo, 'reservation-email');
            $this->stdout($subject . PHP_EOL, Console::FG_YELLOW);
        }
    }


    /**
     * @param AdvertReservation $reservation
     * @param $type
     * @param $tel
     * @param $message
     */
    private function sendSms(AdvertReservation $reservation, $type, $tel, $message)
    {
        if (YII_DEBUG) {
            return;
        }

        if (!$tel) {
            Yii::error('Ошибка при отправке SMS: reservationId = ' . $reservation->id . ' type: ' . $type . ' tel: ' . $tel . ' error: номер телефона не указан', 'reservation-sms');
            return;
        }

        $tel = (substr($tel, 0, 1) === '+') ? $tel : '+' . $tel;
        $tel = (substr($tel, 0, 2) === '+8') ? str_replace('+8', '+7', $tel) : $tel;

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse($tel, "RU");
            $isValid = $phoneUtil->isValidNumber($swissNumberProto);
        } catch (\libphonenumber\NumberParseException $e) {
            $isValid = false;
        }

        if (!$isValid) {
            Yii::error('Ошибка при отправке SMS: reservationId = ' . $reservation->id . ' type: ' . $type . ' tel: ' . $tel . ' error: номер телефона некорректный', 'reservation-sms');
            return;
        }

        $sms = Yii::$app->sms;
        $result = $sms->send_sms($tel, $message . ' - cdaem.ru');
        if ($sms->isSuccess($result)) {
            Yii::info('Успешная Отправка SMS: reservationId = ' . $reservation->id . ' type: ' . $type . ' tel: ' . $tel, 'reservation-sms');
        } else {
            Yii::error('Ошибка при отправке SMS: reservationId = ' . $reservation->id . ' type: ' . $type . ' tel: ' . $tel . ' error: ' . $sms->getError($result), 'reservation-sms');
        }
    }
}
