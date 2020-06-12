<?php

namespace common\modules\partners\services;

use common\modules\partners\interfaces\ServiceInterface;
use frontend\modules\partners\models\Reservation;
use common\modules\partners\models\ReservationsPayment;
use common\modules\partners\models\Service;
use common\modules\users\models\User;
use yii\validators\ExistValidator;
use yii\validators\EmailValidator;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use Yii;

/**
 * Сервис [Contacts Open For Total Bid]
 * Отправить контакты пользователя в общей заявке
 *
 * @package common\modules\partners\services
 */
final class ContactsOpenForTotalBid extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * @var string Идентификатор сервиса
     */
    const NAME = 'CONTACTS_OPEN_FOR_TOTAL_BID';

    /**
     * Идентификатор заявки, у которой пользователь открывает контакты
     * @var int
     */
    private $_reservation_id;

    /**
     * Модель заявки
     * @var \frontend\modules\partners\models\Reservation
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
     * @inheritdoc
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
        if (!$this->_reservation = Reservation::findOne($this->_reservation_id)) {
            return false;
        }

        if ($this->_reservation->cancel) {
            return false;
        }

        if ($this->_reservation->closed) {
            return false;
        }

        if ($this->_reservation->getIsContactsOpen($this->_user->id)) {
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

        $model = new ReservationsPayment();
        $model->reservation_id = $this->_reservation->id;
        $model->user_id = $this->_user->id;
        $model->date_create = date('Y-m-d H:i:s');
        $this->_reservation->closed = 1;
        $this->_reservation->date_update = date('Y-m-d H:i:s');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->_reservation->save(false);
            $model->save(false);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        return true;
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