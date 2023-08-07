<?php

namespace common\modules\agency;

/**
 * Bootstrap Frontend
 * @package common\modules\agency
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
                'agency' => 'agency/default/index',
                [
                    'pattern' => '/agency/komnat/<rooms:\w+>/okrug/<district:\w+>',
                    'route' => '/agency/default/index',
                ],
                'podberem_kvartiry' => 'agency/default/select',
                'advert/<id:\d+>' => '/agency/default/view',
                [
                    'pattern' => '/<rentType:\w+>/komnat/<rooms:\w+>/okrug/<district:\w+>',
                    'route' => '/site/default/index',
                ],
                '<rentType:\w+>' => '/site/default/index',
                //'images/thumbs/<path:.*>' => '/agency/default/thumbs',
            ]
        );
    }
}
