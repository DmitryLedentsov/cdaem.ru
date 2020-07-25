<?php

namespace common\modules\partners\services;

use common\modules\partners\interfaces\ServiceInterface;
use common\modules\partners\models\Service;
use yii\validators\ExistValidator;
use yii\validators\EmailValidator;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use Yii;

/**
 * Сервис [Advert Selected]
 * Реклама в верхнем слайдере
 *
 * @package common\modules\partners\services
 */
final class AdvertSelected extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * @var string Идентификатор сервиса
     */
    const NAME = 'ADVERT_SELECTED';

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
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Выделить объявление цветом';
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
                $percent = 40;
            } else if ($days >= 21) {
                $percent = 30;
            } else if ($days >= 14) {
                $percent = 20;
            } else if ($days >= 7) {
                $percent = 10;
            }

            if ($amount >= 60) {
                $percent += 30;
            } else if ($amount >= 50) {
                $percent += 25;
            } else if ($amount >= 40) {
                $percent += 20;
            } else if ($amount >= 30) {
                $percent += 15;
            } else if ($amount >= 20) {
                $percent += 10;
            } else if ($amount >= 10) {
                $percent += 7;
            } else if ($amount >= 5) {
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
        return true;
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

        // Выделяем объявления цветом
        foreach ($this->_selected as $advertId) {
            $advert = \frontend\modules\partners\models\Advert::findOne($advertId);
            $advert->selected = 1;
            $advert->save(false);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        if (!$this->validate()) {
            return false;
        }

        // Снимаем выделение с объявлений
        foreach ($this->_selected as $advertId) {
            $advert = \frontend\modules\partners\models\Advert::findOne($advertId);
            $advert->selected = 0;
            $advert->save(false);
        }

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
        return true;
    }

    /**
     * @inheritdoc
     */
    public function runBackgroundProcess()
    {
        Yii::$app->consoleRunner->run('service/execute-instant-process ' . $this->_process->id);
    }
}