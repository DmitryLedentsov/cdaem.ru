<?php

namespace common\modules\partners\widgets\frontend\RealtyObjectsByService;

use common\modules\merchant\models\frontend\Pay;
use Yii;
use yii\base\Widget;
use yii\base\InvalidArgumentException;
use common\modules\realty\models\RentType;
use common\modules\partners\models\Service;
use common\modules\partners\models\frontend as models;

/**
 * Генерирует html код объектов пользователя для выбора и активации сервисов
 *
 * Виджет принимает несколько параметров: $service и $userId.
 * Если указанный сервис существует, то на основе него выбираются определенные объекты недвижимости пользователя.
 * Ответ генерируется в html формате для дальнейших манипуляций.
 *
 * @package common\modules\partners\widgets\frontend\RealtyObjectsByService
 */
class RealtyObjectsByService extends Widget
{
    /**
     * Идентификатор сервиса
     * @var int
     */
    public $service;

    /**
     * Идентификатор пользователя
     * @var int
     */
    public $userId;

    /**
     * Идентификатор апарта для которого выводится список
     * @var int
     */
    public $apartmentId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->userId) {
            $this->userId = Yii::$app->user->id;
        }

        if (!in_array($this->service, array_keys(Yii::$app->service->getList()))) {
            throw new InvalidArgumentException('Param "service" must be in services list');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $rentTypeslist = RentType::rentTypeslist();

        switch ($this->service) {
            case Service::SERVICE_APARTMENT_CONTACTS_OPEN:
                $apartments = models\Apartment::findApartmentsByUser($this->userId);
                // Возвращает список всех апартаментов в HTML формате
                return $this->render('apartments', [
                    'models' => $apartments,
                    'rentTypeslist' => $rentTypeslist,
                ]);
                break;

            case Service::SERVICE_ADVERTISING_TOP_SLIDER:
            case Service::SERVICE_ADVERTISING_IN_SECTION:
            case Service::SERVICE_ADVERT_TOP_POSITION:
            case Service::SERVICE_ADVERT_SELECTED:
            case Service::SERVICE_ADVERT_IN_TOP:
            case Service::SERVICE_CONTACTS_OPEN_FOR_TOTAL_BID:
            case Service::SERVICE_CONTACTS_OPEN_FOR_RESERVATION:
                if ($this->apartmentId) {
                    $adverts = models\Advert::getAdvertsByUserAndApartmentId($this->userId, $this->apartmentId);
                }
                else {
                    $adverts = models\Advert::getAdvertsByUser($this->userId);
                }

                // Возвращает список всех объявлений в HTML формате
                return $this->render('adverts', [
                    'models' => $adverts,
                    'rentTypeslist' => $rentTypeslist,
                ]);
                break;
        }
    }
}
