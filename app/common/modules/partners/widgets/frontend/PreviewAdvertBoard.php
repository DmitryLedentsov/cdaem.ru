<?php

namespace common\modules\partners\widgets\frontend;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Виджет отображает блок объявления на доске объявлений
 */
class PreviewAdvertBoard extends Widget
{
    /**
     * @var \common\modules\partners\models\frontend\Advert
     */
    public $advert;

    /**
     * @var string
     */
    private $_rentTypes;

    /**
     * @var string
     */
    private $_address;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->advert->apartment->metroStation) {
            $this->_address = $this->advert->apartment->address;
        } else {
            $this->_address = 'м. ' . $this->advert->apartment->metroStation->metro->name;
        }

        foreach ($this->advert->apartment->adverts as $advert) {
            $icon = '';
            if ($advert->rentType['icons']) {
                $icons = @json_decode($advert->rentType['icons'], true);
                if (isset($icons['small-blue'])) {
                    $icon = $icons['small-blue'];
                }
            }

            $active = '';
            if (mb_strtolower($this->advert['rent_type']) === mb_strtolower($advert['rent_type'])) {
                $active = ' active';
            }

            $href = Yii::$app->urlManager->createUrl([
                '/partners/default/view',
                'id' => $advert->advert_id,
                'city' => $advert->apartment->city->name_eng
            ]);

            $this->_rentTypes .= Html::tag('div', $icon . '<a href=" ' . $href . '">' . '<span class="price">' . $advert->priceText . '</span></a>', [
                'class' => 'item' . $active,
                'title' => $advert->rentType['name']
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $totalRoomsString = Yii::t(
            'app',
            '{n, plural, =0{нет комнат} one{# комната} =5{# комнат}  few{# комнаты} many{# комнат} other{# комнаты} }',
            ['n' => $this->advert->apartment->total_rooms]
        );

        $online = '';
        if ($this->advert->apartment->user->isOnline()) {
            $online = '<span class="online" title="Владелец сейчас на сайте"></span>';
        }

        $now_available = '';
        if ($this->advert->apartment->now_available) {
            $now_available = 'Сейчас свободно';
        }
//        if ($this->advert->apartment->metroStation)
        //var_dump($this->advert->apartment->city->name);
        $selected = $this->advert->selected ? ' apartment-item-selected' : '';
        $contacts = $this->advert->apartment->open_contacts ? '<div class="contacts topper" title="Контакты открыты"></div>' : '';

        return ('<div class="apartment-card">
                <div class="apartment-item'. $selected. '">
                    <div class="apartment-image-block">
                        <!--<div class="apartment-price"><span class="apartment-price-value">'.$this->advert->priceText.'</span> / сут</div>-->
                        <div class="apartment-price apartment-price apartment-prices">'.$this->advert->priceText.' / 24ч</div>
                        '. $this->getPreviewTitleImage().'
                        <button class="apartment-phone"></button>
                    </div>
                    <div class="apartment-metro">
                        <span>До метро:</span>
                        <div class="apartment-metro-item">
                            <object class="apartment-metro-icon">
                                <img src="/_new/images/apartment-card/pedestrian.svg" alt="pedestrian">
                            </object>
                            <span>'. $this->advert->apartment->getTimeToMetroInMinutes(0).'</span>
                        </div>
                        <div class="apartment-metro-item">
                            <object class="apartment-metro-icon">
                                <img src="/_new/images/apartment-card/car.svg" alt="car">
                            </object>
                            <span>'. $this->advert->apartment->getTimeToMetroInMinutes(1).'</span>
                        </div>
                        <div class="apartment-metro-item">
                            <object class="apartment-metro-icon">
                                <img src="/_new/images/apartment-card/bus.svg" alt="bus">
                            </object>
                            <span>'. $this->advert->apartment->getTimeToMetroInMinutes(2).'</span>
                        </div>
                    </div>
                    <div class="apartment-location">'.$this->advert->apartment->city->name.', '.$this->advert->apartment->address.'</div>
                    <div class="apartment-position">Метро: '.$this->_address.'</div>
                    <div class="apartment-info">
                        <div class="apartment-info-item  apartment-rooms">
                            <object class="apartment-icon">
                                <img src="/_new/images/icons/apartment/rooms.svg" alt="rooms">
                            </object>
                            <span>'.$totalRoomsString.'</span>
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
                            <span>'.$this->advert->apartment->beds.' с. места</span>
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
     * Возвращает превью изображение заглавной картинки с необходимыми атрибутами alt и title
     * @return string
     */
    protected function getPreviewTitleImage()
    {
        $rentTypeName = $this->advert->rentType->name;

        $title = $this->advert->apartment->city->name . ', ';
        if ($this->advert->apartment->metroStation) {
            $title .= 'м.' . $this->advert->apartment->metroStation->metro->name;
        } else {
            $title .= $this->advert->apartment->address;
        }
        $alt = $this->advert->rentType->name;
        $alt .= ' ' . $title;

        return Html::a(Html::img($this->advert->apartment->titleImageSrc, [
            'alt' => $alt,
            'title' => $title,
            'class' => 'apartment-image',
        ]), ['/partners/default/view', 'city' => $this->advert->apartment->city->name_eng, 'id' => $this->advert->advert_id]);
    }
}
