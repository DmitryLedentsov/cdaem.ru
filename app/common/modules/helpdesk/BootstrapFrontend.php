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
        $app->urlManager->addRules(
            [
                'vacancy' => '/helpdesk/default/vacancy',
                Yii::$app->params['siteSubDomain'] . '/complaint/<advert_id:\d+>' => '/helpdesk/ajax/complaint',
                'complaint/<advert_id:\d+>' => '/helpdesk/ajax/complaint',
                'help/<url>' => '/helpdesk/default/help',
                'help' => '/helpdesk/default/help',
            ]
        );
    }
}
