<?php

namespace common\modules\helpdesk;

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
        $app->urlManager->addRules(
            [
                'call' => '/helpdesk/default/index',
                'workvac' => '/helpdesk/default/workvac',
                'complaint/<advert_id:\d+>' => '/helpdesk/ajax/complaint',
            ]
        );
    }
}
