<?php

namespace common\modules\partners\widgets\frontend;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use common\modules\users\models\Profile;
use common\modules\partners\models\frontend\AdvertisementSlider;

/**
 * Class AdvertisingTopSliderAdverts
 * @package common\modules\partners\widgets\frontend
 */
class AdvertisingTopSliderAdverts extends Widget
{
    /**
     * @var array
     */
    private $_advertisements;

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
        // Взять id Города
        $city_id = Yii::$app->request->cityId;

        // Ключ кэша, в вариациях города
        $cacheKey = 'TopSliderWidget';
        $cacheKey .= '_' . $city_id;

        // Зависимость кэша
        $cacheDependency1 = new \yii\caching\DbDependency([
            'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )",
            'reusable' => true,
        ]);

        $cacheDependency2 = new \yii\caching\DbDependency([
            'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
            'reusable' => true,
        ]);

        $cacheDependency = new \yii\caching\ChainedDependency([
            'dependencies' => [$cacheDependency1, $cacheDependency2]
        ]);

        // Вынимаем данные из кэша
        $cachedData = Yii::$app->cache->get($cacheKey);

        // Если нет, то сгенерируем
        if ($cachedData === false) {
            $this->_advertisements = AdvertisementSlider::getAdvertisementList(Yii::$app->getModule('partners')->amountAdvertisements, $city_id);

            $advertisements = '';
            foreach ($this->_advertisements as $adv) {
                if ($adv['type'] === AdvertisementSlider::TYPE_RENT) {
                    if ($adv->advert) {
                        $image = $adv->advert->apartment->titleImageSrc;
                    }
                }

                $advertisements .= PreviewAdvertTmp::widget([
                    'advertisement' => $adv,
                ]);
            }

            $user_type = Profile::find()->select('user_type')->where(['user_id' => Yii::$app->user->id])->scalar();

            if ($user_type === Profile::OWNER) {
                $cachedData = '
                <section class="recommendation">
                    <div class="container-fluid">
                        <h2 class="section-title">
                            ' . Html::a('Попасть сюда', ['/office/default/buy-ads']) . '
                        </h2>
                        <div class="recommendation-list">
                            ' . $advertisements . '
                        </div>
                    </div>
                </section>
                ';
            } else {
                $cachedData = '
                <section class="recommendation">
                    <div class="container-fluid">
                        <h2 class="section-title">
                            Рекомендуемые объявления
                        </h2>
                        <div class="recommendation-list">
                            ' . $advertisements . '
                        </div>
                    </div>
                </section>
                ';
            }

            Yii::$app->cache->set($cacheKey, $cachedData, 300, $cacheDependency);
        }

        return $cachedData;
    }
}
