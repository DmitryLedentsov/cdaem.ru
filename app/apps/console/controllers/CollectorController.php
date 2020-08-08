<?php

namespace console\controllers;

use Yii;
use yii\helpers\Console;
use common\modules\users\models\User;
use common\modules\partners\models as models;

/**
 * Сборщик мусора
 * @package common\modules\partners\commands
 */
class CollectorController extends \yii\console\Controller
{
    /**
     * Garbage Collector Images
     * Сборщик мусора
     *
     * Осуществляет поиск старых и/или удаленных изображений
     * Осуществляет чистку диска и базы данных
     *
     * Вызов команды: php yii partners/collector/images
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionImages()
    {
        //models\Image
        $this->stdout('Чистит изображения' . PHP_EOL);

        $path = Yii::getAlias('@frontend/web/partner_imgs') . '/';
        $thumbsPath = Yii::getAlias('@frontend/web/partner_thumb') . '/';
        $result = [];
        // Удаление записей в БД ссылающихся на несуществующие файлы Больших картинок
        $image = true;
        $offset = 0;
        $result['deleted_rows'] = 0;
        while ($image) {
            $image = models\Image::find()->limit(1)->offset($offset)->asArray()->one();
            $offset++;
            if (!$image) {
                continue;
            }
            if (!file_exists($path . $image['review'])) {
                $result['deleted_rows'] += (new \yii\db\Query())
                    ->createCommand()
                    ->delete(models\Image::tableName(), ['image_id' => $image['image_id']])
                    ->execute();
                $offset--;
            }
        }
        // Удаление картинок или любых других файлов с папки, если на них нету записи в БД
        $result['deleted_reviews'] = 0;
        $result['deleted_previews'] = 0;
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.gitignore') {
                    continue;
                }
                if (!is_file($path . $file)) {
                    continue;
                }

                $image = models\Image::find()->where(['review' => $file])->asArray()->one();
                if (!$image) {
                    $result['deleted_reviews'] += @unlink($path . $file);
                    $result['deleted_previews'] += @unlink($thumbsPath . $file);
                }
            }
            closedir($handle);
        }

        // Удаление маленьких файлов
        if ($handle = opendir($thumbsPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.gitignore') {
                    continue;
                }
                if (!is_file($thumbsPath . $file)) {
                    continue;
                }

                $image = models\Image::find()->where(['preview' => $file])->asArray()->one();
                if (!$image) {
                    $result['deleted_previews'] += @unlink($thumbsPath . $file);
                }
            }

            closedir($handle);
        }

        print_r($result);
    }

    /**
     * Garbage Collector Reservation
     * Сборщик мусора
     * Осуществляет поиск старых заявок, проставляет необходимые статусы старым заявкам
     *
     * Вызов команды: php yii partners/collector/reservation
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionReservation()
    {
        //models\Reservation
        $this->stdout('Чистит заявки' . PHP_EOL);
        $count = models\Reservation::updateAll(['closed' => 1], 'date_out <= :now', [':now' => date('Y-m-d H:i:s')]);
        $count += models\AdvertReservation::updateAll(['closed' => 1], 'date_out <= :now', [':now' => date('Y-m-d H:i:s')]);

        return $count;
    }

    public function actionActreservation()
    {
        //models\Reservation
        $this->stdout('Чистит заявки' . PHP_EOL);
        $count = models\Reservation::updateAll(['closed' => 1], 'date_actuality <= :now', [':now' => date('Y-m-d H:i:s')]);
        $count += models\AdvertReservation::updateAll(['closed' => 1], 'date_actuality <= :now', [':now' => date('Y-m-d H:i:s')]);

        return $count;
    }

    /**
     * Reservation verify payment
     * Верификация оплаты подтверждения заявки
     *
     * Сценарий проверяет оплату подтверждения заявки, если одна из сторон не
     * оплатила в течении указанного переиода времени, заявка будет закрыта
     *
     * Вызов команды: php yii partners/collector/reservation-verify-payment
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionReservationVerifyPayment()
    {
        $expire = Yii::$app->getModule('partners')->timeIntervalPaymentReservation;

        $offset = 0;
        $reservation = true;

        while ($reservation) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $reservation = models\AdvertReservation::find()
                    ->joinWith('deal')
                    ->where([
                        'confirm' => [models\AdvertReservation::RENTER, models\AdvertReservation::LANDLORD]
                    ])->limit(1)->offset($offset)->one();

                if (!$reservation) {
                    break;
                }

                // Вернуть средства владельцу
                if ($reservation->confirm == models\AdvertReservation::LANDLORD && $this->checkExpireGone2($reservation->date_actuality)) {
                    $paymentId = Yii::$app->balance
                        ->setModule(Yii::$app->getModule('partners')->id)
                        ->setUser(User::findOne($reservation->landlord_id))
                        ->billing($reservation->deal->funds_owner, models\ReservationDeal::RETURN_MONEY);

                    $reservation->confirm = 0;
                    $reservation->closed = 1;
                    $reservation->date_update = date('Y-m-d H:i:s');
                    $reservation->save(false);
                    $reservation->deal->delete();

                    $params = $reservation->id . ' landlord ' . $reservation->deal->funds_owner;
                    Yii::$app->consoleRunner->run('partners/reservation/send-mail-return-by-not-confirm ' . $params);

                    $offset--;
                } // Вернуть средства клиенту
                elseif ($reservation->confirm == models\AdvertReservation::RENTER && $this->checkExpireGone2($reservation->date_actuality)) {
                    $paymentId = Yii::$app->balance
                        ->setModule(Yii::$app->getModule('partners')->id)
                        ->setUser(User::findOne($reservation->user_id))
                        ->billing($reservation->deal->funds_client, models\ReservationDeal::RETURN_MONEY);

                    $reservation->confirm = 0;
                    $reservation->closed = 1;
                    $reservation->date_update = date('Y-m-d H:i:s');
                    $reservation->save(false);
                    $reservation->deal->delete();

                    $params = $reservation->id . ' renter ' . $reservation->deal->funds_client;
                    Yii::$app->consoleRunner->run('partners/reservation/send-mail-return-by-not-confirm ' . $params);

                    $offset--;
                }

                $transaction->commit();

                Yii::info('Заявка ID' . $reservation->id . ' - возврат денег прошел успешно', 'reservation-verify-payment');
                $this->stdout('SUCCESS: reservation ID ' . $reservation->id . PHP_EOL, Console::FG_GREEN);
            } catch (\Exception $e) {
                $transaction->rollBack();

                Yii::error('Заявка ID' . $reservation->id . ' - Ошибка: [' . $e->getMessage() . ']', 'reservation-verify-payment');
                $this->stdout('FAIL: reservation ID ' . $reservation->id . ' ' . $e->getMessage() . PHP_EOL, Console::FG_RED);
            }

            $offset++;
        }
    }

    /**
     * Возврат денег по заявкам "Незаезд"
     */
    public function actionReservationVerifyFailure()
    {
        $countSuccess = 0;
        $countError = 0;
        $failures = models\ReservationFailure::find()->processed(0)->closed(0)->timeHasCome();

        foreach ($failures->each() as $failure) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($failure->reservation->user_id == $failure->user_id) {
                    // Клиент
                    $fundsToReturn = $failure->reservation->deal->funds_client;

                    //Возврат
                    $paymentId = Yii::$app->balance
                        ->setModule('partners')
                        ->setUser(User::findOne($failure->user_id))
                        ->billing($fundsToReturn, models\ReservationDeal::RETURN_MONEY_FAILURE);

                    $failure->reservation->deal->delete();
                    $failure->reservation->cancel = 3;
                    $failure->reservation->cancel_reason = 'Деньги возвращены по заявке "Незаезд"';
                    $failure->reservation->closed = 1;
                    $failure->reservation->date_update = date('Y-m-d H:i:s');
                    $failure->reservation->save(false);

                    $failure->processed = 1;
                    $failure->save(false);
                }

                if ($failure->reservation->landlord_id == $failure->user_id) {
                    // Владельцу еще и деньги которые заплатил клиент
                    $fundsToReturn = $failure->reservation->deal->funds_owner + $failure->reservation->deal->funds_client;

                    //Возврат
                    $paymentId = Yii::$app->balance
                        ->setModule('partners')
                        ->setUser(User::findOne($failure->user_id))
                        ->billing($fundsToReturn, models\ReservationDeal::RETURN_MONEY_FAILURE);

                    $failure->reservation->deal->delete();
                    $failure->reservation->cancel = 3;
                    $failure->reservation->cancel_reason = 'Деньги возвращены по заявке "Незаезд"';
                    $failure->reservation->closed = 1;
                    $failure->reservation->date_update = date('Y-m-d H:i:s');
                    $failure->reservation->save(false);

                    $failure->processed = 1;
                    $failure->save(false);
                }

                $transaction->commit();

                $params = $failure->id . ' ' . $fundsToReturn;
                Yii::$app->consoleRunner->run('partners/reservation/send-mail-failure-processed ' . $params);

                $countSuccess++;
            } catch (\Exception $e) {
                $transaction->rollBack();
                $countError++;
                Yii::error('Ошибка: [Обработка ID' . $failure->id . ' не прошло]', 'verify-failure');
            }
        }

        $this->stdout('Процес выполнен. Обработано записей: ' . ($countSuccess + $countError) . ', возникло ошибок: ' . $countError . PHP_EOL, Console::FG_GREEN);
        Yii::info('Процес выполнен. Обработано записей: ' . ($countSuccess + $countError) . ', возникло ошибок: ' . $countError, 'verify-failure');
    }

    /**
     * Garbage Collector Service
     * Сборщик мусора
     *
     * Осуществляет поиск старых процессов активации сервиса и удаляет неоплаченные
     *
     * Вызов команды: php yii partners/collector/service
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionService()
    {
        //models\Reservation
        $this->stdout('Чистит сервисы' . PHP_EOL);
        $count = models\Service::deleteAll('process = 1 AND date_expire <= :now', [':now' => date('Y-m-d H:i:s')]);

        return $count;
    }

    /**
     * Apartments Watcher
     *
     * Осуществляет поиск подозрительных объявлений в которых может
     * быть указан номер телефона владельца
     *
     * Вызов команды: php yii partners/collector/apartments-watcher
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionApartmentsWatcher()
    {
        $batch = models\Apartment::find()
            ->andWhere('(suspicious = 0 AND suspicious_detect_date IS NULL) OR (suspicious = 0 AND date_update > suspicious_detect_date)')
            ->batch();

        $count = 0;

        foreach ($batch as $apartments) {
            foreach ($apartments as $apartment) {
                if (!$this->verifySuspicious($apartment->description)) {
                    continue;
                }

                $apartment->suspicious = 1;
                $apartment->suspicious_detect_date = date('Y-m-d H:i:s');
                $apartment->save(false);

                ++$count;
            }
        }

        $this->stdout('Процес выполнен. Обработано объявлений: ' . ($count) . PHP_EOL, Console::FG_GREEN);
        Yii::info('Процес выполнен. Обработано объявлений: ' . ($count), 'apartments-watcher');
    }

    public function actionApartmentsW()
    {
        $batch = models\Apartment::find()
            ->andWhere('(total_rooms > 4)')
            ->all();

        $count = 0;

        foreach ($batch as $apartments) {
            foreach ($apartments as $apartment) {
                if ($apartment->open_contacts != 1) {
                    continue;
                }

                $apartment->open_contacts = 1;
                $apartment->save(false);
            }
        }
    }

    /**open_contacts
     * Проверят прошло ли количество секунд с даты
     * @param $date date format 'Y-m-d H:i:s'
     * @param $expireCondition int секунды
     * @return boolean
     */
    private function checkExpireGone($date, $expireCondition)
    {
        $now = strtotime('now');
        $date = strtotime($date);

        $diff = $now - $date;

        if ($diff > $expireCondition) {
            return true;
        }

        return false;
    }

    private function checkExpireGone2($expireCondition)
    {
        $now = strtotime('now');


        if ($now > $expireCondition) {
            return true;
        }

        return false;
    }

    /**
     * @param $str
     * @return bool
     */
    private function verifySuspicious($str)
    {
        if (
            mb_stripos($str, 'тел') === false &&
            mb_stripos($str, 'т.') === false &&
            mb_stripos($str, 'phone') === false &&
            mb_stripos($str, 'звон') === false &&
            mb_stripos($str, 'номер') === false &&
            mb_stripos($str, 'конт') === false
        ) {
            return true;
        }

        /* $_src = $str;
         $res = preg_replace('/\D/si', '', $_src);

         if (mb_strlen($res) < 7) {
             return true;
         }*/

        if (
            mb_stripos($str, 'один') === false &&
            mb_stripos($str, 'два') === false &&
            mb_stripos($str, 'три') === false &&
            mb_stripos($str, 'четыре') === false &&
            mb_stripos($str, 'пять') === false &&
            mb_stripos($str, 'шесть') === false &&
            mb_stripos($str, 'семь') === false &&
            mb_stripos($str, 'восемь') === false &&
            mb_stripos($str, 'девять') === false &&
            mb_stripos($str, 'ноль') === false
        ) {
            return true;
        }

        return false;
    }
}
