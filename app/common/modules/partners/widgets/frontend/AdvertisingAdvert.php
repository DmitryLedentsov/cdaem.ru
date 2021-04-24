<?php

namespace common\modules\partners\widgets\frontend;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\modules\partners\models\frontend\Advertisement;

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
                $result .= '
                <div class="advertisement-card">
                    <a href="' . Url::toRoute(['/partners/default/view', 'id' => $advert->advert_id, 'city' => $advert->advert->apartment->city->name_eng]) . '">
                        <img src="' . $advert->advert->apartment->titleImageSrc . '" alt="advertisement-image">
                    </a>
                </div>
                ';
            }

            return ('
            <section class="advertisement">
                <div class="container-fluid">
                    <h2 class="section-title">Реклама от владельца</h2>
                    <div class="advertisement-list">
            
                    ' . $result . '
                     
                    </div>
                </div>
            </section>
            ');
        }
    }
}
