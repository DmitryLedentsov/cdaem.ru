<?php

namespace common\modules\partners\widgets\frontend;

use common\modules\partners\models\frontend\Advertisement;
use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * Class AdvertisingAdvert
 */
class AdvertisingAdvert extends \yii\base\Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $advertisement = Advertisement::getRelevantAds(Yii::$app->request->cityId);

        if ($advertisement) {
            $result = '';

            foreach ($advertisement as $advert) {
                $result .= '<a href="' . Url::toRoute(['/partners/default/view', 'id' => $advert->advert_id, 'city' => $advert->advert->apartment->city->name_eng]) . '">
                        <div class="advert-preview cover" title="Карамзана, дом 1, корпус 3">
                        <div class="apartment-wrap">
                            <div class="image">
                                    <img src="' . $advert->advert->apartment->titleImageSrc . '" alt="">
                                </div>
                                <div class="description">
                                    <strong>' . Yii::$app->formatter->asCurrency($advert->advert->price, ArrayHelper::getValue($advert->advert->getCurrencyList(), $advert->advert->currency)) . '</strong>
                                    <p>' . $advert->advert->rentType->name . '</p>
                                    ' . $advert->text . '
                                </div>
                            </div>
                        </div>
                    </a>';
            }

            return ('
                <div class="verified-apartments">
                    <div class="row">
                    <h3><span>Реклама от владельцев</span></h3>
                    ' . $result . '
                    </div>
                    <div class="clearfix"></div>
                </div>
            ');
        }
    }
}
