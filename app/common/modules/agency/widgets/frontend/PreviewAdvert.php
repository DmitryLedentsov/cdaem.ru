<?php

namespace common\modules\agency\widgets\frontend;

use Yii;
use yii\helpers\Html;
use common\modules\agency\models\SpecialAdvert;

/**
 * Class PreviewAdvert
 * @package common\modules\agency\widgets\frontend
 */
class PreviewAdvert extends \yii\base\Widget
{
    /**
     * @var \common\modules\agency\models\Advert
     */
    public $advert;

    /**
     * @var array
     */
    private $_options;

    /**
     * @var string
     */
    private $_district = '';

    /**
     * @var string
     */
    private $_metro = '';

    /**
     * @var string
     */
    private $_metroAll = '';

    /**
     * @var string
     */
    private $_rentTypes;

    private $_rentNew;

    /**
     * @var string
     */
    private $_specialText = '';

    /**
     * @var bool
     */
    private $_specialAdvert = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Спец предложения
        if ($this->advert instanceof SpecialAdvert) {
            $this->_specialText = $this->advert->text;
            $this->_specialAdvert = true;
            $this->advert = $this->advert->advert;
        }

        if ($this->advert->apartment->mainDistrict) {
            $this->_district = $this->advert->apartment->mainDistrict->district_name . ',';
        }

        if ($this->advert->apartment->metroStations) {
            $metroArray = [];
            foreach ($this->advert->apartment->metroStations as $key => $metro) {
                $metroArray[] = $metro->metro->name;
            }

            $this->_metro = 'м. ' . $metroArray[0];

            if (count($metroArray) == 1) {
                $this->_metroAll = $metroArray[0];
            } else {
                unset($metroArray[0]);
                $this->_metroAll = implode(', ', $metroArray);
            }
        }

        // Список типов аренды
        foreach ($this->advert->apartment->adverts as $advert) {
            $icon = '';
            if ($advert->rentType['icons']) {
                $icons = @json_decode($advert->rentType['icons'], true);
                if (isset($icons['small-white'])) {
                    $icon = $icons['small-white'];
                }
            }
            if ($advert->priceText > 0) {
                $this->_rentTypes .= Html::tag(
                    'div',
                    $icon . '<a href="/advert/' . $advert->advert_id . '"><div class="price-d">' . $advert->priceText . '</div></a>',
                    ['title' => $advert->rentType['name'], 'class' => 'advert-infoprice']
                );
            }
        }

        foreach ($this->advert->apartment->adverts as $advert) {
            $icon2 = '';
            if ($advert->rentType['icons']) {
                if (isset($icons['big-star'])) {
                    $icon2 = $icons['big-star'];
                }
            }
            $this->_rentNew = $icon2;
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $href = Yii::$app->urlManager->createUrl([
            '/agency/default/view',
            'id' => $this->advert->advert_id,
        ]);

        $price = '';
        if (is_numeric($this->advert->price) && $this->advert->price > 0) {
            $price = '<div class="price">' . $this->advert->priceText . '</div>';
        }

        $type = '<div class="type">' . $this->_rentTypes . '</div>';

//        if ($this->_specialAdvert) {
//            return $this->getSpecialPreview($href, $price, $type);
//        }

        return $this->getPreview($href, $price, $type);
    }

    /**
     * Возвращает превью изображения заглавной картинки с необходимыми атрибутами alt и title
     * @return string
     */
    protected function getPreviewTitleImage()
    {
        return Html::a(Html::img($this->advert->apartment->titleImageSrc, [
            'alt' => "Фото " . $this->advert->apartment->city->name . " " . $this->advert->apartment->address . " " . $this->advert->rentType->name . " № " . $this->advert->advert_id,
            'title' => "Фото " . $this->advert->apartment->city->name . " " . $this->advert->apartment->address . " " . $this->advert->rentType->name . " № " . $this->advert->advert_id,
            'class' => 'apartment-image'
        ]), ['/agency/default/view', 'id' => $this->advert->advert_id]);
    }

    /**
     * Превью просмотр объявления
     *
     * @param $href
     * @param $price
     * @param $type
     * @return string
     */
    protected function getPreview($href, $price, $type)
    {
        return ('
            <div class="apartment-card">
                <div class="apartment-item">
                    <div class="apartment-image-block">
                        <div class="apartment-price"><span class="apartment-price-value">'.$this->advert->priceText.'</span> / сут</div>
                        ' . $this->getPreviewTitleImage() . '
                        <button class="apartment-phone"></button>
                    </div>
                    <div class="apartment-metro">
                        <span>До метро:</span>
                        <div class="apartment-metro-item">
                            <object class="apartment-metro-icon">
                                <img src="/_new/images/apartment-card/pedestrian.svg" alt="pedestrian">
                            </object>
                            <span>?? минут</span>
                        </div>
                        <div class="apartment-metro-item">
                            <object class="apartment-metro-icon">
                                <img src="/_new/images/apartment-card/car.svg" alt="car">
                            </object>
                            <span>?? минут</span>
                        </div>
                        <div class="apartment-metro-item">
                            <object class="apartment-metro-icon">
                                <img src="/_new/images/apartment-card/bus.svg" alt="bus">
                            </object>
                            <span>?? минут</span>
                        </div>
                    </div>
                    <div class="apartment-location">' . $this->_district . ' ' . $this->_metro . '</div>
                    <div class="apartment-position">Метро: ' . $this->_metroAll . '</div>
                    <div class="apartment-info">
                        <div class="apartment-info-item  apartment-rooms">
                            <object class="apartment-icon">
                                <img src="/_new/images/icons/apartment/rooms.svg" alt="rooms">
                            </object>
                            <span>' . Yii::t('app', '{n, plural, =0{нет комнат} one{# комната} =5{# комнат}  few{# комнаты} many{# комнат} other{# комнаты} }', ['n' => $this->advert->apartment->total_rooms]) . '</span>
                        </div>
                        <div class="apartment-info-item apartment-area">
                            <object class="apartment-icon">
                                <img src="/_new/images/icons/apartment/area.svg" alt="area">
                            </object>
                            <span>'.$this->advert->apartment->total_area.' м2</span>
                        </div>
                        <div class="apartment-info-item  apartment-beds">
                            <object class="apartment-icon">
                                <img src="/_new/images/icons/apartment/beds.svg" alt="beds">
                            </object>
                            <span>'.$this->advert->apartment->beds.' спальных <br>места</span>
                        </div>
                        <div class="apartment-info-item  apartment-repairs">
                            <object class="apartment-icon">
                                <img src="/_new/images/icons/apartment/repairs.svg" alt="repairs">
                            </object>
                            <span>'.$this->advert->apartment->remontName.'</span>
                        </div>
                    </div>
                </div>
                <div class="apartment-buttons">
                    <button class="apartment-button apartment-button--book"><span class="apartment-button-text">Забронировать</span></button>
                    <button class="apartment-button apartment-button--contact"><span class="apartment-button-text">Связаться</span></button>
                </div>
            </div>
        ');
    }

    /**
     * Превью просмотр спец. объявления
     *
     * @param $href
     * @param $price
     * @param $type
     * @return string
     */
    protected function getSpecialPreview($href, $price, $type)
    {
        return ('
            <div class="apartment-agency special-adv">
                <a href="' . $href . '">
                      <div class="header">
                        <div class="address">' . $this->_district . ' ' . $this->_metro . '</div>
                        <div class="rooms">' . Yii::t('app', '{n, plural, =0{нет комнат} one{# комната} =5{# комнат}  few{# комнаты} many{# комнат} other{# комнаты} }', ['n' => $this->advert->apartment->total_rooms]) . '</div>
                        <div class="clearfix"></div>
                     </div>
                    <div class="image">
                  
                        ' . $this->getPreviewTitleImage() . '
                        <div class="group-adv clearfix">
                            <div class="type" style="font-size:70%; float:none">' . $this->_specialText . '</div>
                        </div>
                        <div class="metro">
                            Метро: ' . $this->_metroAll . '
                        </div>
                    </div>
                </a>
            </div>
        ');
    }
}
