<?php

namespace common\modules\partners\services;

use Yii;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use yii\validators\ExistValidator;
use common\modules\partners\models\Service;
use common\modules\partners\interfaces\ServiceInterface;

/**
 * Сервис [Advert Top Position]
 * Поднять позицию объявлений
 *
 * @package common\modules\partners\services
 */
final class AdvertTopPosition extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * @var string Идентификатор сервиса
     */
    const NAME = 'ADVERT_TOP_POSITION';

    /**
     * EMAIL пользователя
     * @var string
     */
    private $_email;

    /**
     * Выбранные объявления
     * @var array
     */
    private $_selected;

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
        return 10;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Поднять позицию объявления';
    }

    /**
     * @inheritdoc
     */
    public function calculateDiscount($amount, array $data = [])
    {
        $days = isset($data['days']) ? (int)$data['days'] : 1;
        $price = isset($data['price']) ? $data['price'] : 1;

        if ($amount >= 3 || $days >= 7) {
            $percent = 0;

            if ($days >= 28) {
                $percent = 25;
            } elseif ($days >= 21) {
                $percent = 20;
            } elseif ($days >= 14) {
                $percent = 10;
            } elseif ($days >= 7) {
                $percent = 5;
            }

            if ($amount >= 60) {
                $percent += 30;
            } elseif ($amount >= 50) {
                $percent += 25;
            } elseif ($amount >= 40) {
                $percent += 20;
            } elseif ($amount >= 30) {
                $percent += 15;
            } elseif ($amount >= 20) {
                $percent += 10;
            } elseif ($amount >= 10) {
                $percent += 7;
            } elseif ($amount >= 5) {
                $percent += 5;
            }

            $discount = round(($price / 100) * $percent);

            if ($discount > 0) {
                return $discount;
            }
        }

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
        return false;
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
        $this->_selected = isset($data['selected']) ? (array)$data['selected'] : [];
        $this->_email = $this->_process->user ? $this->_process->user->email : null;


        // Проверяем почтовый адрес
        $EmailValidator = new EmailValidator();
        if (!$EmailValidator->validate($this->_email)) {
            return false;
        }

        // Проверяем объявления
        foreach ($this->_selected as $advertId) {
            $ExistValidator = new ExistValidator();
            $ExistValidator->targetClass = '\frontend\modules\partners\models\Advert';
            $ExistValidator->targetAttribute = 'advert_id';

            if (!$ExistValidator->validate($advertId)) {
                return false;
            }
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

        $CalculatePosition = new \common\modules\partners\services\auxiliaries\CalculatePosition();
        $CalculatePosition->setSelectedAdverts($this->_selected);
        $CalculatePosition->updateAdvertsPositions();

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
            'view' => null,
            'data' => [

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
