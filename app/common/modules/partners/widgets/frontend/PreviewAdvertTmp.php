<?php

namespace common\modules\partners\widgets\frontend;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidArgumentException;
use common\modules\partners\models\frontend\Advert;
use common\modules\partners\models\frontend\Apartment;
use common\modules\partners\models\frontend\AdvertisementSlider;

/**
 * Виджет отображает блок объявления в различных вариантах.
 * @package common\modules\partners\widgets
 */
class PreviewAdvertTmp extends Widget
{
    /**
     * @var \common\modules\partners\models\frontend\Apartment
     */
    public $apartment;

    /**
     * @var \common\modules\partners\models\frontend\AdvertisementSlider
     */
    public $advertisement;

    /**
     * @var \common\modules\partners\models\frontend\Advert
     */
    public $advert;

    /**
     * Включить отображение позиций у объявления
     * @var bool
     */
    public $enableAdvertPosition = false;

    /**
     * Массив для Url::to()
     * @var array
     */
    public $customUrl = null;

    /**
     * Дополнительные опции для блока
     * @var array
     */
    private $_previewExtraOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!empty($this->advert) && $this->advert instanceof Advert) {
            $this->_previewExtraOptions = [
                'data' => [
                    'advert' => $this->advert->advert_id,
                    'rent-type' => $this->advert->rent_type,
                ],
                'title' => $this->advert->apartment->address,
            ];
        } elseif (!empty($this->apartment) && $this->apartment instanceof Apartment) {
            $this->_previewExtraOptions = [
                'data' => [
                    'apartment' => $this->apartment->apartment_id,
                ],
                'title' => $this->apartment->address,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $selected = '';
        $present = '';
        $advert = null;


        if (!empty($this->advert) && $this->advert instanceof Advert) {
            $advert = $this->advert;
            $present = $this->getAdvertByAdvert();

            if ($this->advert->selected) {
                $selected = ' selected';
            }
        } elseif (!empty($this->advertisement) && $this->advertisement instanceof AdvertisementSlider) {
            $advert = $this->advertisement->advert;
            $present = $this->getAdvertByAdvertisement();

            if ($this->advertisement->advert && $this->advertisement->advert->selected) {
                $selected = ' selected';
            }
        } elseif (!empty($this->apartment) && $this->apartment instanceof Apartment) {
            $present = $this->getAdvertByApartment();
        } else {
            throw new InvalidArgumentException('Param "Advert" or "Advertisement" or "Apartment" must be correct set');
        }


        $result = Html::tag('div', $present, ['class' => 'apartment-wrap']);
        $result = $this->verifyAdvertPosition() . "\n" . $result;
        $result = Html::tag('div', $result, array_merge(['class' => 'advert-preview cover' . $selected], $this->_previewExtraOptions));


        if (!$advert) {
            return $result;
        }

        if ($this->customUrl !== null) {
            $url = $this->customUrl;
        } else {
            $url = [
                '/partners/default/view', 'id' => $advert->advert_id,
                'city' => $advert->apartment->city->name_eng
            ];
        }

        return Html::a($result, $url);
    }

    /**
     * Данные апартаментов
     * @return string
     */
    protected function getInfoByAdvert($apartment, $type)
    {
        if ($type == 'online' && $apartment->user->isOnline()) {
            return '<span class="online" title="Владелец сейчас на сайте"></span>';
        }

        if ($type == 'now_available' && $apartment->now_available) {
            return 'Сейчас свободно';
        }

        if ($type == 'no-bg') {
            if (!$apartment->now_available) {
                return ' no-bg';
            }

            return '';
        }

        return '';
    }

    /**
     * Генератор блока объявления
     * @return string
     */
    protected function getAdvertByAdvert()
    {
        return '
                <div class="apartment-card">
                    <div class="apartment-item">
                        <div class="apartment-image-block">
                            <div class="apartment-price apartment-prices">'.$this->advert->priceText.' / 24ч</div>
                            <img class="apartment-image" src="'.$this->advert->apartment->titleImageSrc.'" alt="advert-image">
                            <button class="apartment-phone"></button>
                        </div>
                        <div class="apartment-period">Квартира '.$this->advert->rentType->name.'</div>
                        <div class="apartment-location">
                            '.$this->advert->apartment->city->name.', '. $this->advert->apartment->address  .'
                        </div>
                        <div class="apartment-position">Удаленность от метро: ' . $this->advert->apartment->metroWalkText . '</div>
                        <div class="apartment-info">
                            <div class="apartment-info-item  apartment-rooms">
                                <object class="apartment-icon">
                                    <img src="./images/icons/apartment/rooms.svg" alt="rooms">
                                </object>
                                <span>' . $this->advert->apartment->rooms . ' комната</span>
                            </div>
                            <div class="apartment-info-item apartment-area">
                                <object class="apartment-icon">
                                    <img src="./images/icons/apartment/area.svg" alt="area">
                                </object>
                                <span>' . $this->advert->apartment->total_area . ' м2</span>
                            </div>
                            <div class="apartment-info-item  apartment-beds">
                                <object class="apartment-icon">
                                    <img src="./images/icons/apartment/beds.svg" alt="beds">
                                </object>
                                <span>' . $this->advert->apartment->beds . ' спальных <br>места</span>
                            </div>
                            <div class="apartment-info-item  apartment-repairs">
                                <object class="apartment-icon">
                                    <img src="./images/icons/apartment/repairs.svg" alt="repairs">
                                </object>
                                <span>' . $this->advert->apartment->remontName . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="apartment-buttons">
                        <button class="apartment-button apartment-button--book"><span class="apartment-button-text">Забронировать</span></button>
                        <button class="apartment-button apartment-button--contact"><span class="apartment-button-text">Связаться</span></button>
                    </div>
                </div>
            ';
    }

    /**
     * Генератор блока апартаментов
     * @return string
     */
    protected function getAdvertByApartment()
    {
        return '
        <div class="image">
            <img src="' . $this->apartment->titleImageSrc . '" alt="" />
            <div class="desc' . $this->getInfoByAdvert($this->apartment, 'no-bg') . '">
                ' . $this->getInfoByAdvert($this->apartment, 'now_available') . '
                ' . $this->getInfoByAdvert($this->apartment, 'online') . '
            </div>
        </div>
        <div class="description">
            <strong>№ ' . $this->apartment->apartment_id . '</strong>
        </div>';
    }

    /**
     * Генератор рекламного блока объявления
     * @return string
     */
    protected function getAdvertByAdvertisement()
    {
        $present = '';

        if ($this->advertisement->type != AdvertisementSlider::TYPE_CLIENT && $this->advertisement->type != AdvertisementSlider::TYPE_RESERV) {
            //$present .= '<div class="image"><img src="/partner_thumb/{{ advertisement.advert.apartment.titleImage.preview }}" alt=""></div>';
            $present .= '<div class="recommendation-card">
                            <img src="' . $this->advertisement->advert->apartment->titleImageSrc . '" alt="recommendation-image">
                            <button class="recommendation-phone"></button>';
        } else {
            $present .= '<div class="image"><img src="' . Yii::$app->getModule('partners')->imageUrlUserReserve . '" alt=""></div>';
        }

        $more_info = ($this->advertisement->more_info) ? $this->advertisement->more_info : '';

        if ($this->advertisement->label == 5) {
            $label = Yii::$app->formatter->asCurrency($this->advertisement->advert->price, ArrayHelper::getValue($this->advertisement->advert->getCurrencyList(), $this->advertisement->advert->currency));
        } else {
            $label = ArrayHelper::getValue(AdvertisementSlider::getLabelArray(), $this->advertisement->label);
        }

        $present .= '
            <div class="recommendation-text">
                ' . $label . '
            </div>
        </div>';

        return $present;
    }

    /**
     * Показывает значек позиции объявления
     * @return string
     */
    protected function verifyAdvertPosition()
    {
        if ($this->enableAdvertPosition === true && !empty($this->advert)) {
            if ($this->advert->position < $this->advert->old_position) {
                $positionClass = 'up';
                $positionText = 'Позиция поднята';
            } elseif ($this->advert->position > $this->advert->old_position) {
                $positionClass = 'down';
                $positionText = 'Позиция опущена';
            } else {
                $positionClass = 'no-change';
                $positionText = 'Без изменений';
            }

            return Html::tag('div', '', [
                'class' => 'position ' . $positionClass,
                'title' => $positionText,
            ]);
        }

        return '';
    }
}
