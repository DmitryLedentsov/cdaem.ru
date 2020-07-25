<?php

namespace common\modules\partners\services;

use common\modules\partners\interfaces\ServiceInterface;
use frontend\modules\partners\models\AdvertReservation;
use common\modules\partners\models\Service;
use common\modules\users\models\User;
use yii\validators\EmailValidator;
use yii\helpers\Json;
use Yii;

/**
 * Сервис [Contacts Open For Reservation]
 * Отправить контакты пользователя в зарезервированной заявке
 *
 * @package common\modules\partners\services
 */
final class ContactsOpenForReservation extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * Идентификатор сервиса
     * @var string
     */
    const NAME = 'CONTACTS_OPEN_FOR_RESERVATION';

    /**
     * Идентификатор заявки, у которой пользователь открывает контакты
     * @var int
     */
    private $_reservation_id;

    /**
     * Модель заявки
     * @var \frontend\modules\partners\models\AdvertReservation
     */
    private $_reservation;

    /**
     * Модель пользователя
     * @var \common\modules\users\models\User
     */
    private $_user;

    /**
     * Инстанс процесса сервиса
     * @var \common\modules\partners\models\Service
     */
    private $_process;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return self::NAME;
    }

    /**
     * @return double
     */
    public function getPrice()
    {
        return 50;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Открыть контакты клиента';
    }

    /**
     * @inheritdoc
     */
    public function calculateDiscount($amount, array $data = [])
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function isTimeInterval()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isInstant()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function setProcess(Service $process)
    {
        $this->_process = $process;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $data = Json::decode($this->_process->data);
        $this->_reservation_id = isset($data['reservation_id']) ? $data['reservation_id'] : [];

        if (isset($data['user_id'])) {
            $this->_user = User::findOne($data['user_id']);
            if (!$this->_user) {
                return false;
            }
        } else {
            return false;
        }

        // Проверяем объявление
        if (!$this->_reservation = AdvertReservation::findOne($this->_reservation_id)) {
            return false;
        }

        if ($this->_reservation->cancel) {
            return false;
        }

        if ($this->_reservation->landlord_open_contacts == 1 or $this->_reservation->confirm == 3) {
            return false;
        }

        // Проверяем почтовый адрес
        $EmailValidator = new EmailValidator();
        if (!$EmailValidator->validate($this->_user->email)) {
            return false;
        }

        return true;
    }

    public function validateContact()
    {

        return true;
    }

    public function validateContactOpen()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function enable()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_reservation->date_update = date('Y-m-d H:i:s');
        $this->_reservation->landlord_open_contacts = 1;

        return $this->_reservation->save(false);
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getMailData()
    {
        return [
            'subject' => null,
            'view' => 'service-open-contacts-reservation',
            'data' => [
                'reservation' => $this->_reservation,
                'user' => $this->_user
            ],
            'email' => $this->_user->email,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSmsString()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isNeedRollBackProcess()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function runBackgroundProcess()
    {
        Yii::$app->consoleRunner->run('service/execute-instant-process ' . $this->_process->id);
    }
}