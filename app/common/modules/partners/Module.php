<?php

namespace common\modules\partners;

use Yii;

/**
 * Общий модуль [[Partners]]
 * Осуществляет всю работу с апартаментами доски объявлений
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $imagesWatermarkPath = '@frontend/web/basic-images/watermark.png';

    /**
     * @var string
     */
    public $imagesTmpPath = '@frontend/web/tmp/partners';

    /**
     * @var string
     */
    public $imagesPath = '@frontend/web/partner_imgs';

    /**
     * @var string
     */
    public $imagesUrl = '/partner_imgs';

    /**
     * @var string
     */
    public $previewImagesPath = '@frontend/web/partner_thumb';

    /**
     * @var string
     */
    public $previewImagesUrl = '/partner_thumb';

    /**
     * @var string
     */
    public $imageMaxSize = 7340032; // 7MB

    /**
     * @var string
     */
    public $imageResizeWidth = 1000;

    /**
     * @var string
     */
    public $previewImageResizeWidth = 300;

    /**
     * @var string
     */
    public $maxUploadImages = 10;

    /**
     * @var string
     */
    public $defaultImageSrc = '/basic-images/no_img.png';

    /**
     * @var string
     */
    public $imageUrlUserReserve = '/basic-images/user-reservation.png';

    /**
     * @var integer
     */
    public $pageSize = 70;

    /**
     * Цена рекламного объявления
     * @var decimal
     */
    public $priceAdvertisement = 30;

    /**
     * Кол-во рекламных объявлений
     * @var integer
     */
    public $amountAdvertisements = 7;

    /**
     * Процент, который платит владелец при подтверждении заявки
     * @var decimal
     */
    public $ownerReservationPercent = 5;

    /**
     * Процент, который платит клиент при подтверждении заявки
     * @var decimal
     */
    public $clientReservationPercent = 5;

    /**
     * Время, в течении которого необходимо оплатить заявку на резервацию
     * @var int
     */
    public $timeIntervalPaymentReservation = 86400;

    /**
     * Через какое время после даты заезда заявки на бронь обеими сторонами можна отправить "Не заезд" (1 день)
     * @var int
     */
    public $timeIntervalForReservationFailStart = 86400;

    /**
     * Через какое время после даты заезда заявки на бронь обеими сторонами уже нельзя отправлять "Не заезд" (2 дня)
     */
    public $timeIntervalForReservationFailEnd = 172800;

    /**
     * Через какое время нужно вернуть деньги после подачи заявки "Не заезд" (10 дней)
     * @var int
     */
    public $timeIntervalToProcessFailedReserves = 864000;
}