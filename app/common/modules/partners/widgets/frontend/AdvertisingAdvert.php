<?php

namespace common\modules\partners\widgets\frontend;

use common\modules\geo\models\City;
use Yii;
use yii\helpers\Url;
use common\modules\partners\models\frontend\Advertisement;

class AdvertisingAdvert extends \yii\base\Widget
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run(): string
    {
        $pathArray = explode('/',Yii::$app->request->pathInfo);
        $currentAdvertId = $pathArray[count($pathArray)-1];
        $currentAdvertId = isset($currentAdvertId) ? (int) $currentAdvertId : null;
        $cityId = City::findByNameEng(Yii::$app->request->getCurrentCitySubDomain() ?: 'msk')->city_id;
        $advertisements = Advertisement::getRelevantAds($cityId);

        if ($advertisements) {
            $result = '';

            foreach ($advertisements as $advertisement) {
                if ($currentAdvertId && $advertisement->advert_id !== $currentAdvertId) {
                    $result .= '
                <div class="advertisement-card">
                    <div class="advertisement-price">
                        <span>' . $advertisement->advert->priceText . '</span> / ' . $advertisement->advert->rentType->short_name . '
                    </div>
                    <a class="advertisement-img" href="' . Url::toRoute(['/partners/default/view', 'id' => $advertisement->advert_id, 'city' => $advertisement->advert->apartment->city->name_eng]) . '">
                        <img src="' . $advertisement->advert->apartment->titleImageSrc . '" alt="advertisement-image">
                    </a>
                    <div class="advertisement-address">
                        ' . $advertisement->advert->apartment->city->name . ', ' . $advertisement->advert->apartment->address . '
                    </div>   
                </div>
                ';
                }
            }

            return ('
            <section class="advertisement">
                <div class="container-fluid">
                    <h2 class="section-title">Другие предложения</h2>
                    <div class="advertisement-list">
                        ' . $result . '
                    </div>
                </div>
            </section>
            ');
        }

        return '';
    }
}
