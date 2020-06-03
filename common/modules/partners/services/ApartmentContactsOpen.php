<?php

namespace common\modules\partners\services;

use common\modules\partners\interfaces\ServiceInterface;
use common\modules\partners\models\AdvertReservation;
use common\modules\partners\models\Service;
use yii\validators\ExistValidator;
use yii\validators\EmailValidator;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use Yii;

/**
 * Сервис [Apartment Contacts Open]
 * Отправить контакты апартаментов
 *
 * @package common\modules\partners\services
 */
final class ApartmentContactsOpen extends \yii\base\Object implements ServiceInterface {

    /**
     * @var string Идентификатор сервиса
     */
    const NAME = 'APARTMENT_CONTACTS_OPEN';

    /**
     * EMAIL пользователя
     * @var string
     */
    private $_email;

    /**
     * Выбранные апартаменты
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
    public function getId() {
        return self::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getPrice() {
        return 5;
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'Открыть контакты';
    }

    /**
     * @inheritdoc
     */
    public function calculateDiscount($amount, array $data = []) {
        $days = isset($data['days']) ? (int) $data['days'] : 1;
        $price = isset($data['price']) ? $data['price'] : 1;

        if ($amount >= 3 || $days >= 7) {

            $percent = 0;

            if ($days >= 28) {
                $percent = 25;
            } else if ($days >= 21) {
                $percent = 20;
            } else if ($days >= 14) {
                $percent = 15;
            } else if ($days >= 7) {
                $percent = 10;
            }

            if ($amount >= 60) {
                $percent += 50;
            } else if ($amount >= 50) {
                $percent += 45;
            } else if ($amount >= 40) {
                $percent += 40;
            } else if ($amount >= 30) {
                $percent += 35;
            } else if ($amount >= 20) {
                $percent += 25;
            } else if ($amount >= 10) {
                $percent += 15;
            } else if ($amount >= 5) {
                $percent += 10;
            }

            $discount = round(($price / 100) * $percent);

            if ($discount > 0) {
                return $discount;
            }
        }

        return 0;
    }

    /* public function calculateDiscount($amount, array $data = [])
      {
      $days = isset($data['days']) ? (int)$data['days'] : 1;
      $price = isset($data['price']) ? $data['price'] : 1;

      if ($amount >= 3 || $days >= 7) {
      $percent = 0;
      $counter = ($amount > $days) ? $amount : $days;

      for ($i = 1; $i <= $counter; $i++) {
      $percent += $i;
      }

      $percent /= 2;
      $percent = $percent > 50 ? 50 : $percent;

      $discount = round(($price / 100) * $percent);

      if ($discount > 0) {
      return $discount;
      }
      }

      return 0;
      } */

    /**
     * @inheritdoc
     */
    public function isTimeInterval() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isInstant() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function setProcess(Service $process) {
        $this->_process = $process;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate() {
        $data = Json::decode($this->_process->data);
        $this->_selected = isset($data['selected']) ? (array) $data['selected'] : [];
        $this->_email = $this->_process->user ? $this->_process->user->email : null;

        // Проверяем почтовый адрес
        $EmailValidator = new EmailValidator();
        if (!$EmailValidator->validate($this->_email)) {
            return false;
        }

        // Проверяем объявления
        foreach ($this->_selected as $apartmentId) {
            $ExistValidator = new ExistValidator();
            $ExistValidator->targetClass = '\frontend\modules\partners\models\Apartment';
            $ExistValidator->targetAttribute = 'apartment_id';

            if (!$ExistValidator->validate($apartmentId)) {
                return false;
            }
        }

        return true;
    }

    //////////////Проверка на бронь для открытия контактов

    public function validateContact() {
        $data = Json::decode($this->_process->data);
        $this->_selected = isset($data['selected']) ? (array) $data['selected'] : [];
        $this->_email = $this->_process->user ? $this->_process->user->email : null;

        foreach ($this->_selected as $apartmentId) {
            $apartment = \frontend\modules\partners\models\Apartment::findOne($apartmentId);
            foreach ($apartment->adverts as $advert) {
                $reservationwidth = AdvertReservation::find()
                                ->Where(['=', 'landlord_id', Yii::$app->user->id])
                                ->andWhere(['!=', 'confirm', 3])
                                ->andWhere(['!=', 'closed', true])
                                ->andWhere(['advert_id' => $advert->advert_id])->one();
            }

            if (!empty($reservationwidth))
                return false;
        }

        return true;
    }

    ////////////////

    public function validateContactOpen() {
        $data = Json::decode($this->_process->data);
        $this->_selected = isset($data['selected']) ? (array) $data['selected'] : [];
        $this->_email = $this->_process->user ? $this->_process->user->email : null;
        //$this->_process;
        foreach ($this->_selected as $apartmentId) {
            $apartment = \frontend\modules\partners\models\Apartment::findOne($apartmentId);
            $proceses = \common\modules\partners\models\Service::findProcessesInQueueApartament($this->_process->user->id);
            if ($apartment->open_contacts == 1) {
                $isopen[] = $apartment;
                
            } 
           if(!empty($proceses)) {
             return false; 
            }
            
              if (!empty($isopen)){
                return false;
            }
          
                
        }


        return true;
    }

    /////////////////

    /**
     * @inheritdoc
     */
    public function enable() {
        if (!$this->validate()) {
            return false;
        }

        // Открываем контакты
        foreach ($this->_selected as $apartmentId) {
            $apartment = \frontend\modules\partners\models\Apartment::findOne($apartmentId);
            $apartment->open_contacts = 1;
            $apartment->save(false);
        }

        // Проверяем, открыл ли пользователь все контакты
        $apartmentsByUser = \frontend\modules\partners\models\Apartment::findApartmentsByUser($this->_process->user_id);
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

        return true;
    }

    /**
     * @inheritdoc
     */
    public function disable() {
        if (!$this->validate()) {
            return false;
        }

        // Закрываем контакты
        foreach ($this->_selected as $apartmentId) {
            $apartment = \common\modules\partners\models\Apartment::find()
                    ->joinWith('adverts')
                    ->where([\common\modules\partners\models\Apartment::tableName() . '.apartment_id' => $apartmentId])
                    ->one();
            if ($apartment) {
                $isTop = false;
                foreach ($apartment->adverts as $advert) {
                    if ($advert->top) {
                        $isTop = true;
                        break;
                    }
                }
                if (!$isTop) {
                    $apartment->open_contacts = 0;
                    $apartment->save(false);
                }
            }
        }

        // Если у пользователя закрываются контакты хотябы одного объявления,
        // то он теряет статус VIP
        $this->_process->user->profile->vip = 0;
        $this->_process->user->profile->save(false);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getMailData() {
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
    public function getSmsString() {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isNeedRollBackProcess() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function runBackgroundProcess() {
        Yii::$app->consoleRunner->run('service/execute-instant-process ' . $this->_process->id);
    }

}
