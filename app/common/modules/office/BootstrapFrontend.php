<?php

namespace common\modules\office;

/**
 * Class BootstrapFrontend
 * @package common\modules\office
 */
class BootstrapFrontend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.reservations
        $app->urlManager->addRules(
            [
                'office' => 'office/default/index',
                'office/orders' => '/office/default/orders',
                'office/top-slider' => '/office/default/buy-ads',
                'office/top-slider-history' => 'office/default/top-slider',
                'office/services' => 'office/default/services',
                'office/fast-settling' => 'office/default/fast-settling',
                'office/send-offer' => 'office/default/send-offer',
                'office/bookmark' => 'office/default/bookmark',
                'office/blacklist' => 'office/default/blacklist',
                'office/ajax/<a:\w+>' => '/office/ajax/<a>',
                'office/ajax/delete-top-slider/<advertisement_id:\d+>' => '/office/ajax/delete-top-slider',
            ]
        );
    }
}
