<?php

namespace common\modules\partners\services;

use Yii;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use yii\validators\ExistValidator;
use common\modules\partners\models\Service;
use common\modules\partners\interfaces\ServiceInterface;

/**
 * Сервис [Advertising In Section]
 * Реклама объявлений в разделе
 *
 * @package common\modules\partners\services
 */
final class AdvertisingInSection extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * Идентификатор сервиса
     */
    const NAME = 'ADVERTISING_IN_SECTION';

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
        return 'Реклама объявления в разделе';
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
                $percent = 15;
            } elseif ($days >= 7) {
                $percent = 10;
            }

            if ($amount >= 60) {
                $percent += 40;
            } elseif ($amount >= 50) {
                $percent += 35;
            } elseif ($amount >= 40) {
                $percent += 30;
            } elseif ($amount >= 30) {
                $percent += 25;
            } elseif ($amount >= 20) {
                $percent += 20;
            } elseif ($amount >= 10) {
                $percent += 17;
            } elseif ($amount >= 5) {
                $percent += 15;
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
            $ExistValidator->targetClass = \common\modules\partners\models\Advert::class;
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

        $data = Json::decode($this->_process->data);
        $advertisementIds = [];

        // Создаем рекламное объявление
        foreach ($this->_selected as $advertId) {
            $advertisement = new \common\modules\partners\models\Advertisement();
            $advertisement->advert_id = $advertId;
            $advertisement->save(false);
            $advertisementIds[] = $advertisement->advertisement_id;
        }

        $data['advertisements'] = $advertisementIds;
        $this->_process->data = Json::encode($data);
        $this->_process->save(false);

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

        $data = Json::decode($this->_process->data);
        if (isset($data['advertisements'])) {
            foreach ($data['advertisements'] as $advertisementId) {
                $advertisement = \common\modules\partners\models\Advertisement::findOne($advertisementId);
                if ($advertisement) {
                    $advertisement->delete();
                }
            }
        } else {
            // Удаляем рекламное объявление
            foreach ($this->_selected as $advertId) {
                $advertisement = \common\modules\partners\models\Advertisement::findOne(['advert_id' => $advertId]);
                if ($advertisement && strtotime(date('Y-m-d H:i:s')) >= strtotime($advertisement->date_expire)) {
                    $advertisement->delete();
                }
            }
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
