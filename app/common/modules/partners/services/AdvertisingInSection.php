<?php

namespace common\modules\partners\services;

use Yii;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use yii\validators\ExistValidator;
use yii\validators\UniqueValidator;
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
    private string $email;

    /**
     * Выбранные объявления
     * @var array
     */
    private array $selected;

    /**
     * Инстанс процесса сервиса
     * @var \common\modules\partners\models\Service
     */
    private Service $process;

    /**
     * @inheritdoc
     */
    public function getId(): string
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
    public function isTimeInterval(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isInstant(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function setProcess(Service $process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(): bool
    {
        $data = Json::decode($this->process->data);
        $this->selected = isset($data['selected']) ? (array)$data['selected'] : [];
        $this->email = $this->process->user ? $this->process->user->email : null;


        // Проверяем почтовый адрес
        $emailValidator = new EmailValidator();
        if (!$emailValidator->validate($this->email)) {
            return false;
        }

        // Проверяем объявления
        $advertIdColumn = 'advert_id';
        foreach ($this->selected as $advertId) {
            $advertExistValidator = new ExistValidator();
            $advertExistValidator->targetClass = \common\modules\partners\models\Advert::class;
            $advertExistValidator->targetAttribute = $advertIdColumn;

            if (!$advertExistValidator->validate($advertId)) {
                return false;
            }

            //Проверка объявления на уникальность, то есть что его реклама не была уже куплена.
            //TODO: поменять логику проверки рекламы, чтобы до оплаты

            /* $advertisementExistValidator = new ExistValidator();
            $advertisementExistValidator->targetClass = \common\modules\partners\models\Advertisement::class;
            $advertisementExistValidator->targetAttribute = $advertIdColumn;

            if ($this->uniqueCheck===true && $advertisementExistValidator->validate($advertId)) {
                return false;
            }*/
        }

        return true;
    }

    public function validateContact(): bool
    {
        return true;
    }

    public function validateContactOpen(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function enable(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $data = Json::decode($this->process->data);
        $advertisementIds = [];

        // Создаем рекламное объявление
        foreach ($this->selected as $advertId) {
            $advertisement = new \common\modules\partners\models\Advertisement();
            $advertisement->advert_id = $advertId;
            $advertisement->save(false);
            $advertisementIds[] = $advertisement->advertisement_id;
        }

        $data['advertisements'] = $advertisementIds;
        $this->process->data = Json::encode($data);
        $this->process->save(false);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function disable(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $data = Json::decode($this->process->data);
        if (isset($data['advertisements'])) {
            foreach ($data['advertisements'] as $advertisementId) {
                $advertisement = \common\modules\partners\models\Advertisement::findOne($advertisementId);
                if ($advertisement) {
                    $advertisement->delete();
                }
            }
        } else {
            // Удаляем рекламное объявление
            foreach ($this->selected as $advertId) {
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
    public function getMailData(): array
    {
        return [
            'subject' => null,
            'view' => null,
            'data' => [

            ],
            'email' => $this->email,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSmsString(): ?string
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isNeedRollBackProcess(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function runBackgroundProcess()
    {
        Yii::$app->consoleRunner->run('service/execute-instant-process ' . $this->process->id);
    }
}
