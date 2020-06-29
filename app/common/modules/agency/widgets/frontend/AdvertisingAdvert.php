<?php

namespace common\modules\agency\widgets\frontend;

use common\modules\agency\models\Advertisement;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/**
 * Advertising Advert
 * @package common\modules\agency\widgets\frontend
 */
class AdvertisingAdvert extends \yii\base\Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->request->get('city') != 'msk') {
            return \frontend\modules\partners\widgets\AdvertisingAdvert::widget();
        }

        $advertisement = Advertisement::getRelevantAds();

        if ($advertisement && mt_rand(1, 2) == 1) {
            $result = '';
            foreach ($advertisement as $advert) {
                $price = null;
                if ($advert->advert->price > 0) {
                    $price = Yii::$app->formatter->asCurrency($advert->advert->price, ArrayHelper::getValue($advert->advert->getCurrencyList(), $advert->advert->currency));
                    $price = Html::tag('strong', $price);
                }

                $alt = $advert->advert->apartment->titleImageAlt;
                $title = $advert->advert->apartment->titleImageTitle;

                $result .= '<a href="' . Url::toRoute(['/agency/default/view', 'id' => $advert->advert_id]) . '">
                    <div class="advert-preview cover shadow-box" title="' . $title . '">
                    <div class="apartment-wrap">
                        <div class="image">
                                <img src="' . $advert->advert->apartment->titleImageSrc . '" alt="' . $alt . '">
                            </div>
                            <div class="description">
                                ' . $price . '
                                <p>' . $advert->advert->rentType->name . '</p>
                                ' . $advert->text . '
                            </div>
                        </div>
                    </div>
                </a>';
            }

            return ('
                <div class="verified-apartments col-md-4">
                    <div class="row">
                    <h3><span>Проверенные квартиры</span> от Cdaem.ru</h3>
                    ' . $result . '
                    </div>
                    <div class="clearfix"></div>
                </div>
            ');
        }

        return \frontend\modules\partners\widgets\AdvertisingAdvert::widget();
    }
}
