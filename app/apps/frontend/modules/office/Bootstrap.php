<?php

namespace frontend\modules\office;

use Yii;

/**
 * Office module bootstrap class.
 * @package frontend\modules\office
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
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

                'help/<url>' => '/office/default/help',
                'help' => '/office/default/help',
            ]
        );
    }
}
