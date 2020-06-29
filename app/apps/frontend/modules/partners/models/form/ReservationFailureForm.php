<?php

namespace frontend\modules\partners\models\form;

use common\modules\partners\models\AdvertReservation;
use common\modules\partners\models\ReservationFailure;
use common\modules\users\models\User;
use Yii;

/**
 * @package frontend\modules\partners\models\form
 */
class ReservationFailureForm extends ReservationFailure
{
    /**
     * Резервация на которой произошел "Не заезд"
     * AdvertReservation
     */
    private $_reservation;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'default' => ['reservation_id', 'cause_text']
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'default' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['cause_text', 'string', 'min' => 50],
            [['cause_text', 'reservation_id'], 'required'],
            ['reservation_id', 'checkAccessForFail'],
        ];
    }

    public function checkAccessForFail()
    {
        $this->_reservation = AdvertReservation::find()->where(['id' => $this->reservation_id])
            ->andWhere(['OR', 'user_id = :userId', 'landlord_id = :userId'])
            ->confirm(AdvertReservation::BOTH)
            ->addParams([':userId' => Yii::$app->user->id])->one();


        if (!$this->_reservation) {
            $this->addError('reservation_id', 'Возникла критическая ошибка.');
            return false;
        }

        $timeArrived = strtotime($this->_reservation->date_arrived);
        $timeStart = $timeArrived + $this->module->timeIntervalForReservationFailStart;    // Начало возможности отправить заявку "Не заезд"
        $timeEnd = $timeArrived + $this->module->timeIntervalForReservationFailEnd;      // Конец

        $now = time();

        if ($now > $timeEnd) {
            $this->addError('reservation_id', 'Время подачи заявки "Не заезд" истекло.');
            return false;
        }

        if ($now < $timeStart) {
            $this->addError('reservation_id', 'Время подачи заявки "Не заезд" еще не наступило.');
            return false;
        }

        if (AdvertReservation::checkAlreadyFailed($this->reservation_id)) {
            $this->addError('reservation_id', 'Заявка "Незаезд" уже создана. Прочтите email или обратитесь в службу технической поддержки.');
            return false;
        }
    }

    /**
     * Создание
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = ReservationFailure::getDb()->beginTransaction();

        try {
            $result = $this->createInternal();
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function createInternal()
    {
        $now = new \DateTime('now');
        $this->user_id = Yii::$app->user->id;
        $this->date_create = $now->format('Y-m-d H:i:s');
        $this->date_update = $this->date_create;
        $this->date_to_process = $now->add(new \DateInterval('PT' . $this->module->timeIntervalToProcessFailedReserves . 'S'))->format('Y-m-d H:i:s');

        $this->_reservation->date_update = date('Y-m-d H:i:s');

        // Если программа дошла сюда, то заявка "Незаезд" может быть только от оппонента
        $opponentFailure = ReservationFailure::findOne(['reservation_id' => $this->reservation_id]);

        if ($opponentFailure) {
            // И если все же оппонентская заявка есть, то выключить автоматическую обработку
            $opponentFailure->closed = 1;
            $opponentFailure->conflict = 1;
            $this->conflict = 1;
            $this->closed = 1;
        }

        if (!$this->_reservation->save(false) OR !$this->save(false)) {
            return false;
        }

        if ($opponentFailure AND !$opponentFailure->save(false)) {
            return false;
        }


        return true;
    }
}