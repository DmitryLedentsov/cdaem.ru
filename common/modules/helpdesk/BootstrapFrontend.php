<?php

namespace common\modules\helpdesk;

use Yii;

/**
 * Bootstrap Frontend
 * @package common\modules\helpdesk
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
                'call' => '/helpdesk/default/index',
                'complaint/<advert_id:\d+>' => '/helpdesk/ajax/complaint',
            ]
        );
    }
}