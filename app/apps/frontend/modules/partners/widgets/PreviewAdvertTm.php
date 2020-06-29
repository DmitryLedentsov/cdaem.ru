<?php

namespace frontend\modules\partners\widgets;

use frontend\modules\partners\models\AdvertisementSlider;
use frontend\modules\partners\models\Apartment;
use frontend\modules\partners\models\Advert;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\base\Widget;
use yii\helpers\Html;
use Yii;

/**
 * Виджет отображает блок объявления в различных вариантах.
 * @package frontend\modules\partners\widgets
 */
class PreviewAdvertTm extends Widget
{
    /**
     * @var \frontend\modules\partners\models\Apartment
     */
    public $apartment;

    /**
     * @var \frontend\modules\partners\models\AdvertisementSlider
     */
    public $advertisement;

    /**
     * @var \frontend\modules\partners\models\Advert
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
        }

        else if (!empty($this->apartment) && $this->apartment instanceof Apartment) {
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

        } else if (!empty($this->advertisement) && $this->advertisement instanceof AdvertisementSlider) {

            $advert = $this->advertisement->advert;
            $present = $this->getAdvertByAdvertisement();

            if ($this->advertisement->advert && $this->advertisement->advert->selected) {
                $selected = ' selected';
            }

        } else if (!empty($this->apartment) && $this->apartment instanceof Apartment) {

            $present = $this->getAdvertByApartment();

        } else {

            throw new InvalidParamException('Param "Advert" or "Advertisement" or "Apartment" must be correct set');

        }

        $result = Html::tag('div', $present, ['class' => 'apartment-wrap']);
        $result = $this->verifyAdvertPosition() . "\n" . $result;
        $result = Html::tag('div', $result, array_merge(['class' => 'advert-preview-2 cover' . $selected], $this->_previewExtraOptions));

        if (!$advert) {
            return $result;
        }

        if($this->customUrl !== null) {
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
        <div class="image">
            <img src="' . $this->advert->apartment->titleImageSrc . '" alt="" />
            <div class="desc' . $this->getInfoByAdvert($this->advert->apartment, 'no-bg') . '">
                ' . $this->getInfoByAdvert($this->advert->apartment, 'now_available') . '
                ' . $this->getInfoByAdvert($this->advert->apartment, 'online') . '
            </div>
        </div>
        <div class="description">
            <strong>' . Yii::$app->formatter->asCurrency($this->advert->price, ArrayHelper::getValue($this->advert->getCurrencyList(), $this->advert->currency)) . '</strong>
            ' . $this->advert->rentType->name . '
        </div>';
    }

    /**
     * Генератор блока апартаментов
     * @return string
     */
    protected function getAdvertByApartment()
    {
        return '
        <div class="image" >
            <img src="' . $this->apartment->titleImageSrc . '" alt="" data-lightbox="example-set" />
            <div class="desc' . $this->getInfoByAdvert($this->apartment, 'no-bg') . '">
                ' . $this->getInfoByAdvert($this->apartment, 'now_available') . '
                ' . $this->getInfoByAdvert($this->apartment, 'online') . '
            </div>
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
            $present .= '<div class="image">
                            <img src="' . $this->advertisement->advert->apartment->titleImageSrc . '" alt=""/>
                            <div class="desc' . $this->getInfoByAdvert($this->advertisement->advert->apartment, 'no-bg') . '">
                                ' . $this->getInfoByAdvert($this->advertisement->advert->apartment, 'now_available') . '
                                ' . $this->getInfoByAdvert($this->advertisement->advert->apartment, 'online') . '
                            </div>
                        </div>';
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
            <div class="description">
                <strong>' . $label . '</strong>
                ' . $more_info .
            '</div>';

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
            } else if ($this->advert->position > $this->advert->old_position) {
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