<?php

namespace common\modules\partners\widgets\frontend;

use yii\base\Widget;
use yii\helpers\Url;

/**
 * Информационный блок типов аренды
 * @package common\modules\partners\widgets\frontend
 */
class RentTypePriceInfo extends Widget
{
    /**
     * @var \common\modules\partners\models\frontend\Advert
     */
    public $adverts;

    /**
     * @var \common\modules\partners\models\frontend\Advert
     */
    public $advert;

    /**
     * @var string
     */
    protected $_rentTypeHtmlB;

    /**
     * @var string
     */
    protected $_rentTypeHtmlN;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $rentTypeTime = null;
        $rentTypeText = null;

        if ($this->advert->rentType) {
            $rentTypeText = $this->advert->rentType->name;
            if ($this->advert->rentType->short_name) {
                $rentTypeText = $this->advert->rentType->short_name;
            }
        }

        foreach ($this->adverts as $advert) {
            $rFlag = 0;

            // Квартира на чаc
            if ($advert->rent_type == 1) {
                $rentTypeTime = 'hour';
                $rFlag = 1;
            } // Квартира на сутки
            elseif ($advert->rent_type == 2) {
                $rentTypeTime = 'day';
                $rFlag = 1;
            } // Квартира на ночь
            elseif ($advert->rent_type == 3) {
                $rentTypeTime = 'night';
                $rFlag = 1;
            } // Квартира на месяц
            elseif ($advert->rent_type == 4) {
                $rentTypeTime = 'month';
                $rFlag = 1;
            }

            if ($rFlag) {
                $active = ($this->advert->rent_type == $advert->rent_type) ? ' class="active"' : '';
                $this->_rentTypeHtmlN .= '<li ' . $active . ' title="' . $advert->rentType->name . '"><a href="' . Url::toRoute(['/partners/default/view', 'id' => $advert->advert_id, 'city' => $advert->apartment->city->name_eng]) . '"><span class="icon-rent-type icon-rent-type-widget-' . $rentTypeTime . '"></span></a></li>';
            }
        }

        $this->_rentTypeHtmlB .= '
                <div class="r-item">
                    <h4>' . $this->advert->priceText . '</h4>
                     <span>' . $rentTypeText . '</span>
                </div>';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return '
        <div class="rent-type board">
            <div class="rent-type-w">
                ' . $this->_rentTypeHtmlB . '
                <ul class="links">
                    ' . $this->_rentTypeHtmlN . '
                </ul>
            </div>
        </div>';
    }
}
