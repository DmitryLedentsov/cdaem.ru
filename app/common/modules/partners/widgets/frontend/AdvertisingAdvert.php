<?php

namespace common\modules\partners\widgets\frontend;

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
        $advertisements = Advertisement::getRelevantAds(Yii::$app->request->cityId);

        if ($advertisements) {
            $result = '';

            foreach ($advertisements as $advertisement) {
                $result .= '
                <div class="advertisement-card">
                    <div class="advertisement-price">
                        <span>'. $advertisement->advert->priceText .'</span> / '. $advertisement->advert->rentType->short_name .'
                    </div>
                    <a class="advertisement-img" href="' . Url::toRoute(['/partners/default/view', 'id' => $advertisement->advert_id, 'city' => $advertisement->advert->apartment->city->name_eng]) . '">
                        <img src="' . $advertisement->advert->apartment->titleImageSrc . '" alt="advertisement-image">
                    </a>
                    <div class="advertisement-address">
                        '.$advertisement->advert->apartment->city->name.', '.$advertisement->advert->apartment->address.'
                    </div>   
                    <!--<div class="advertisement-metro">
                        Метро: ---
                    </div>-->
                    <a class="advertisement-link" href="' . Url::toRoute(['/partners/default/view', 'id' => $advertisement->advert_id, 'city' => $advertisement->advert->apartment->city->name_eng]) . '">
                        Посмотреть предложение
                    </a>
                </div>
                ';
            }

            return ('
            <section class="advertisement">
                <div class="container-fluid">
                    <h2 class="section-title">Реклама от владельцев</h2>
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
