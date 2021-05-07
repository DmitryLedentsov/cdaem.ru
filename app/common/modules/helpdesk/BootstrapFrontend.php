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
                'vacancy' => '/helpdesk/default/vacancy',
                'complaint/<advert_id:\d+>' => '/helpdesk/ajax/complaint',
                'help/<url>' => '/helpdesk/default/help',
                'help' => '/helpdesk/default/help',
            ]
        );
    }
}
