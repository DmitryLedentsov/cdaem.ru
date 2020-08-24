<?php

namespace common\modules\partners\services;

use Yii;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use common\modules\users\models\User;
use common\modules\partners\models\Service;
use common\modules\partners\models\Advert;
use common\modules\partners\interfaces\ServiceInterface;

/**
 * Сервис [Service Contacts Open To User]
 * Отправить контакты владельца
 *
 * @package common\modules\partners\services
 */
final class ContactsOpenToUser extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * @var string Идентификатор сервиса
     */
    const NAME = 'CONTACTS_OPEN_TO_USER';

    /**
     * Идентификатор объявления, у которого пользователь открывает контакты
     * @var int
     */
    private $_advert_id;

    /**
     * EMAIL пользователя
     * @var string
     */
    private $_email;

    /**
     * Объявление
     * @var \common\modules\partners\models\Advert
     */
    private $_advert;

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
        return 30;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Открыть контакты владельца';
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
        $this->_advert_id = isset($data['advert_id']) ? $data['advert_id'] : [];
        $this->_email = isset($data['email']) ? $data['email'] : null;

        if (isset($data['user_id'])) {
            $user = User::findOne($data['user_id']);
            if ($user) {
                $this->_email = $user->email;
            }
        }

        // Проверяем объявление
        if (!$this->_advert = Advert::findOne($this->_advert_id)) {
            return false;
        }

        // Проверяем почтовый адрес
        $EmailValidator = new EmailValidator();
        if (!$EmailValidator->validate($this->_email)) {
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

        // Выполнение сервиса заключается в отправке письма с контактными данными пользователя

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
            'view' => 'service-open-contacts-apartments',
            'data' => [
                'advertUrl' => \yii\helpers\Url::to(['/partners/default/view', 'id' => $this->_advert->advert_id, 'city' => $this->_advert->apartment->city->name_eng]),
                'user' => $this->_advert->apartment->user,
            ],
            'email' => $this->_email,
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
