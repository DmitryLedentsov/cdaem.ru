<?php

namespace common\modules\partners\models\frontend\form;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\modules\users\models\User;
use common\modules\partners\models\frontend\Advert;
use common\modules\partners\models\ReservationDeal;
use common\modules\partners\models\frontend\Reservation;
use common\modules\partners\models\frontend\CalendarBlockerForm;

/**
 * Class ReservationConfirmForm
 * @package common\modules\partners\models\frontend\form
 */
class ReservationConfirmForm extends Reservation
{
    /**
     * Тип пользователя который принимает заявку: Владелец апартаментов или Клиент
     * @var string
     */
    public $userType;

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
            'reopen' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cancel_reason'], 'required'],
            ['cancel_reason', 'string', 'min' => 10, 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'date_update',
                ],
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->scenario == 'cancel') {
                if (!$this->defineCancel()) {
                    return false;
                }
                $this->closed = 1;
            }

            if ($this->scenario == 'confirm') {
                if (!$this->userType = $this->defineUser()) {
                    $this->addError('userType', 'Вы не можете управлять данной заявкой');

                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Определяет значение cancel
     * @return bool
     */
    private function defineCancel()
    {
        if ($this->advert_id != null and $this->deal) {
            $this->addError('cancel_reason', 'Вы не можете отменить данную заявку, так как заявка ожидает подтверждения');

            return false;
        }

        if (!$this->userType = $this->defineUser()) {
            $this->addError('userType', 'Вы не можете управлять данной заявкой');

            return false;
        }

        // Отмена клиентом заявки
        if ($this->user_id == Yii::$app->user->id) {
            $this->cancel = 1;

            return true;
        }

        // Отмена владельцем заявки
        if ($this->landlord_id == Yii::$app->user->id) {
            $this->cancel = 2;

            return true;
        }

        return false;
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
            ->where(['reservation_id' => $this->reservation_id])
            ->one();

        if ($this->userType == 'owner') {
            // Уже платил
            if ($deal && $deal->payment_owner == 1) {
                $this->addError('userType', 'Вы уже подтверждали заявку');

                return false;
            }
            // Если валюта не РУБЛЬ
            if ($this->advert->currency != 1) {
                $this->addError('userType', 'Для подтверждения заявки валюта стоимости вашего объявления должна быть "Рубль"');

                return false;
            }

            $advert_price = $this->advert->price;
            $percentage = $this->module->ownerReservationPercent;
            $priceToPay = $advert_price / 100 * $percentage;
            $user_id = $this->user_id;

            // Если не хватает денег
            if ($this->advert->apartment->user->funds_main < $priceToPay) {
                $this->addError('userType', 'На вашем счету недостаточно средств');

                return false;
            }


            $paymentId = Yii::$app->balance
                ->setModule($this->module->id)
                ->setUser(User::findOne(Yii::$app->user->id))
                ->costs($priceToPay, ReservationDeal::CONFIRM_OWNER);
        }

        if ($this->userType == 'client') {
            // Уже платил
            if ($deal && $deal->payment_client == 1) {
                $this->addError('userType', 'Вы уже подтверждали заявку');

                return false;
            }
            // Если валюта не РУБЛЬ
            if ($this->advert->currency != 1) {
                $this->addError('userType', 'Для подтверждения заявки валюта стоимости объявления должна быть "Рубль"');

                return false;
            }

            $advert_price = $this->advert->price;
            $percentage = $this->module->clientReservationPercent;
            $priceToPay = $advert_price / 100 * $percentage;

            // Если не хватает денег
            if ($this->user->funds_main < $priceToPay) {
                $this->addError('userType', 'На вашем счету недостаточно средств');

                return false;
            }

            $paymentId = Yii::$app->balance
                ->setModule($this->module->id)
                ->setUser(User::findOne(Yii::$app->user->id))
                ->costs($priceToPay, ReservationDeal::CONFIRM_CLIENT);
        }

        if (!$deal) {
            $deal = new ReservationDeal;
            $deal->reservation_id = $this->reservation_id;
        }

        $deal->funds = $priceToPay;

        if ($this->userType == 'owner') {
            $deal->payment_owner = 1;
            $deal->date_payment_owner = date('Y-m-d H:i:s');
            if ($this->confirm == 0) {
                $this->confirm = 2;
            } else {
                $this->confirm = 3;
                $calendar = new CalendarBlockerForm();
                $calendar->reserved = 1;
                $calendar->apartment_id = Advert::find()->select('apartment_id')->where(['advert_id' => $this->advert_id])->scalar();
                $calendar->date_from = $this->date_arrived;
                $calendar->date_to = $this->date_out;
                $calendar->process(false);
            }
        }
        if ($this->userType == 'client') {
            $deal->payment_client = 1;
            $deal->date_payment_client = date('Y-m-d H:i:s');
            if ($this->confirm == 0) {
                $this->confirm = 1;
            } else {
                $this->confirm = 3;
                $calendar = new CalendarBlockerForm();
                $calendar->reserved = 1;
                $calendar->apartment_id = Advert::find()->select('apartment_id')->where(['advert_id' => $this->advert_id])->scalar();
                $calendar->date_from = $this->date_arrived;
                $calendar->date_to = $this->date_out;
                $calendar->process(false);
            }
        }

        $this->save(false);
        $deal->save(false);

        return true;
    }
}
