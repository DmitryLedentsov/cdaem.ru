<?php

namespace common\modules\partners\services;

use Yii;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use common\modules\partners\models\Service;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models\Apartment;
use common\modules\partners\interfaces\ServiceInterface;

/**
 * Сервис [Advert In Top]
 * Поднять объявления в топ
 *
 * @package common\modules\partners\services
 */
final class AdvertInTop extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * Идентификатор сервиса
     */
    const NAME = 'ADVERT_IN_TOP';

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
        return 20;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Поднять объявление в ТОП';
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
                $percent = 35;
            } elseif ($days >= 21) {
                $percent = 30;
            } elseif ($days >= 14) {
                $percent = 20;
            } elseif ($days >= 7) {
                $percent = 10;
            }

            if ($amount >= 60) {
                $percent += 50;
            } elseif ($amount >= 50) {
                $percent += 45;
            } elseif ($amount >= 40) {
                $percent += 43;
            } elseif ($amount >= 30) {
                $percent += 30;
            } elseif ($amount >= 20) {
                $percent += 27;
            } elseif ($amount >= 10) {
                $percent += 25;
            } elseif ($amount >= 5) {
                $percent += 22;
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

        //TODO: Ситуация, когда пользователь удаляет свое оъявление
        // Проверяем объявления
        /*foreach ($this->_selected as $advertId) {
            $ExistValidator = new ExistValidator();
            $ExistValidator->targetClass = '\frontend\modules\partners\models\Advert';
            $ExistValidator->targetAttribute = 'advert_id';

            if (!$ExistValidator->validate($advertId)) {
                return false;
            }
        }*/

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

        // Поднимаем объявления в топ
        foreach ($this->_selected as $advertId) {
            $advert = Advert::findOne($advertId);
            $advert->top = 1;
            $advert->save(false);

            // Когда объявление попадает в топ, контакты открываются автоматически
            $advert->apartment->open_contacts = 1;
            $advert->apartment->save(false);
        }

        // Проверяем, открыл ли пользователь все контакты
        $apartmentsByUser = Apartment::findApartmentsByUser($this->_process->user_id);
        $vip = true;
        foreach ($apartmentsByUser as $apartment) {
            if (!$apartment->open_contacts) {
                $vip = false;
                break;
            }
        }
        // Если пользователь открыл контакты во всех объявлениях, делаем его VIP
        if ($vip) {
            $this->_process->user->profile->vip = 1;
            $this->_process->user->profile->save(false);
        }

        $this->updateAdvertsPositions();

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

        // Поднимаем объявления в топ
        foreach ($this->_selected as $advertId) {
            $advert = Advert::find()
                ->with([
                    'apartment' => function ($query) {
                        $query->with(['city']);
                    },
                    'rentType'
                ])
                ->where(['advert_id' => $advertId])
                ->one();

            if (!$advert) {
                continue;
            }

            $advert->top = 0;
            $advert->old_position = $advert->position;
            $advert->position = Advert::getLastPosition($advert->apartment->city_id, $advert->rent_type) + 1;
            $advert->real_position = $advert->position;
            $advert->save(false);

            // Когда объявление вылетает из топа, контакты закрываются автоматически
            $advert->apartment->open_contacts = 0;
            $advert->apartment->save(false);
        }

        // Если у пользователя закрываются контакты хотябы одного объявления,
        // то он теряет статус VIP
        $this->_process->user->profile->vip = 0;
        $this->_process->user->profile->save(false);


        $this->updateAdvertsPositions();

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

    /**
     * Обновить позиции объявлений
     */
    private function updateAdvertsPositions()
    {
        $CalculatePosition = new \common\modules\partners\services\auxiliaries\CalculatePosition();
        $CalculatePosition->setSelectedAdverts($this->_selected);
        $CalculatePosition->updateAdvertsPositions();
    }
}
