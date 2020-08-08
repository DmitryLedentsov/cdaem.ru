<?php

namespace common\modules\agency\widgets\frontend;

use yii\base\Widget;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Информационный блок типов аренды
 */
class RentTypePriceInfoNew extends Widget
{
    /**
     * @var \common\modules\agency\models\Advert
     */
    public $adverts;

    /**
     * @var \common\modules\agency\models\Advert
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
            } elseif ($advert->rent_type == 9) {
                $rentTypeTime = 'star';
                $rFlag = 1;
            }

            if ($rFlag) {
                $active = ($this->advert->rent_type == $advert->rent_type) ? ' class="active"' : '';
                $this->_rentTypeHtmlN .= '<li ' . $active . ' title="' . $advert->rentType->name . '"><a href="' . Url::toRoute(['/agency/default/view', 'id' => $advert->advert_id]) . '"><span class="icon-rent-type-b icon-rent-type-widget-b-' . $rentTypeTime . '"></span></a></li>';
            }
        }

        $advertText = $this->advert->text ? $this->advert->text : 'Нет описания';

        $this->_rentTypeHtmlB .= '
                <div class="br-item">
                    <h4>' . $rentTypeText . ' <span>' . $this->advert->priceText . '</span></h4>
                    <div class="txt">
                        ' . $advertText . '
                    </div>
                </div>';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $_rentTypeHtmlN = '';
        if ($this->_rentTypeHtmlN) {
            $_rentTypeHtmlN = Html::tag('ul', $this->_rentTypeHtmlN, ['class' => 'links']);
        }

        $_rentTypeHtmlB = Html::tag('div', $this->_rentTypeHtmlB . $_rentTypeHtmlN, ['class' => 'rent-type-b']);

        return Html::tag('div', $_rentTypeHtmlB, ['class' => 'rent-type-b']);
    }
}
