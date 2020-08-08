<?php

namespace frontend\modules\partners\models\form;

use Yii;
use common\modules\users\models\User;
use frontend\modules\partners\models\Advert;
use common\modules\partners\models\ReservationDeal;
use frontend\modules\partners\models\AdvertReservation;
use frontend\modules\partners\models\CalendarBlockerForm;

/**
 * @package frontend\modules\partners\models\form
 */
class AdvertReservationConfirmForm extends AdvertReservation
{
    public $error;

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
    public function scenarios()
    {
        return [
            'cancel' => ['cancel_reason'],
            'confirm' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cancel_reason'], 'required', 'on' => 'cancel'],
            ['cancel_reason', 'string', 'min' => 1, 'max' => 255],
        ];
    }

    /**
     * Отменяет эту заяявку
     * @return bool
     */
    public function cancel()
    {
        if (Yii::$app->user->id == $this->user_id) {
            $this->cancel = AdvertReservation::RENTER;
        }
        if (Yii::$app->user->id == $this->landlord_id) {
            $this->cancel = AdvertReservation::LANDLORD;
        }
        $this->closed = 1;
        $this->date_update = date('Y-m-d H:i:s');

        return $this->save();
    }

    /**
     * Создает запись в таблице partners_apartment_reservations_deal
     * @return bool
     */
    public function confirm()
    {
        if (!$this->validate()) {
            return false;
        }

        $deal = ReservationDeal::find()
            ->where(['reservation_id' => $this->id])
            ->one();

        if (Yii::$app->user->id == $this->landlord_id) {
            // Уже платил
            if ($deal && $deal->payment_owner == 1) {
                $this->addError('error', 'Вы уже подтверждали заявку');

                return false;
            }

            // Если валюта не РУБЛЬ
            if ($this->advert->currency != 1) {
                $this->addError('error', 'Для подтверждения заявки валюта стоимости вашего объявления должна быть "Рубль"');

                return false;
            }

            $advert_price = $this->advert->price;
            $percentage = $this->module->ownerReservationPercent;
            $priceToPay = $advert_price / 100 * $percentage;
            $user_id = $this->user_id;

            // Если не хватает денег
            if ($this->landlord->funds_main < $priceToPay) {
                $this->addError('error', 'На вашем счету недостаточно средств');

                return false;
            }


            $paymentId = Yii::$app->balance
                ->setModule($this->module->id)
                ->setUser(User::findOne(Yii::$app->user->id))
                ->costs($priceToPay, ReservationDeal::CONFIRM_OWNER);
        }

        if (Yii::$app->user->id == $this->user_id) {
            // Уже платил
            if ($deal && $deal->payment_client == 1) {
                $this->addError('error', 'Вы уже подтверждали заявку');

                return false;
            }
            // Если валюта не РУБЛЬ
            if ($this->advert->currency != 1) {
                $this->addError('error', 'Для подтверждения заявки валюта стоимости объявления должна быть "Рубль"');

                return false;
            }

            $advert_price = $this->advert->price;
            $percentage = $this->module->clientReservationPercent;
            $priceToPay = $advert_price / 100 * $percentage;

            // Если не хватает денег
            if ($this->user->funds_main < $priceToPay) {
                $this->addError('error', 'На вашем счету недостаточно средств');

                return false;
            }

            $paymentId = Yii::$app->balance
                ->setModule($this->module->id)
                ->setUser(User::findOne(Yii::$app->user->id))
                ->costs($priceToPay, ReservationDeal::CONFIRM_CLIENT);
        }

        if (!$deal) {
            $deal = new ReservationDeal;
            $deal->reservation_id = $this->id;
        }

        if (Yii::$app->user->id == $this->landlord_id) {
            $deal->funds_owner = $priceToPay;
        } else {
            $deal->funds_client = $priceToPay;
        }


        if (Yii::$app->user->id == $this->landlord_id) {
            $deal->payment_owner = 1;
            $deal->date_payment_owner = date('Y-m-d H:i:s');
            if ($this->confirm == 0) {
                $this->confirm = AdvertReservation::LANDLORD;
            } else {
                $this->confirm = AdvertReservation::BOTH;
                $calendar = new CalendarBlockerForm();
                $calendar->reserved = 1;
                $calendar->apartment_id = Advert::find()->select('apartment_id')->where(['advert_id' => $this->advert_id])->scalar();
                $calendar->date_from = $this->date_arrived;
                $calendar->date_to = $this->date_out;
                $calendar->process(false);
            }
        }
        if (Yii::$app->user->id == $this->user_id) {
            $deal->payment_client = 1;
            $deal->date_payment_client = date('Y-m-d H:i:s');
            if ($this->confirm == 0) {
                $this->confirm = AdvertReservation::RENTER;
            } else {
                $this->confirm = AdvertReservation::BOTH;
                $calendar = new CalendarBlockerForm();
                $calendar->reserved = 1;
                $calendar->apartment_id = Advert::find()->select('apartment_id')->where(['advert_id' => $this->advert_id])->scalar();
                $calendar->date_from = $this->date_arrived;
                $calendar->date_to = $this->date_out;
                $calendar->process(false);
            }
        }

        $this->date_update = date('Y-m-d H:i:s');
        $this->save(false);
        $deal->save(false);

        return true;
    }
}
