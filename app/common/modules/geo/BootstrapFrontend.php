<?php

namespace common\modules\geo;

use Yii;

/**
 * Bootstrap Frontend
 * @package common\modules\seo
 */
class BootstrapFrontend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->urlManager->addRules([
            Yii::$app->params['siteSubDomain'] . '/map' => '/geo/default/index',
            'geo/select-city' => '/geo/ajax/select-city',
            'geo/select-city-by-api' => '/geo/ajax/select-city-by-api',
            'geo/select-address-by-api' => '/geo/ajax/select-address-by-api',
            'geo/metro-msk' => '/geo/ajax/metro-msk',
            'geo/nearest-stations' => '/geo/ajax/nearest-stations',
            'geo/map/<typeId:\w+>' => '/geo/ajax/apartment',
            'geo/map' => '/geo/ajax/map',
            'map' => '/geo/default/index',
        ]);
    }
}
