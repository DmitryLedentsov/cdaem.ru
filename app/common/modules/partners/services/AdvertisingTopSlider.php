<?php

namespace common\modules\partners\services;

use Yii;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use common\modules\partners\models\Service;
use common\modules\partners\interfaces\ServiceInterface;
use common\modules\partners\models\frontend\AdvertisementSlider;

/**
 * Сервис [Advertising Top Slider]
 * Выделить объявления
 *
 * @package common\modules\partners\services
 */
final class AdvertisingTopSlider extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * Идентификатор сервиса
     */
    const NAME = 'ADVERTISING_TOP_SLIDER';

    /**
     * Рекламное объявление
     * @var \common\modules\partners\models\frontend\AdvertisementSlider
     */
    private $_advertisement;

    /**
     * EMAIL пользователя, который открывает контакты, если он гость
     * @var int
     */
    private $_email;

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
        return 60;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Попасть в рекламный слайдер';
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

        $advertisementId = isset($data['advertisementId']) ? $data['advertisementId'] : null;
        $this->_email = $this->_process->user ? $this->_process->user->email : null;

        if (!$this->_advertisement = AdvertisementSlider::findOne($advertisementId)) {
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

        $this->_advertisement->payment = 1;
        $this->_advertisement->date_payment = date('Y-m-d H:i:s');

        return $this->_advertisement->save(false);
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
            'view' => 'service-advertising-top-slider',
            'data' => [
                'advertisement' => $this->_advertisement,
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
