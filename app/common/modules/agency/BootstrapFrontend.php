<?php

namespace common\modules\agency;

use Yii;

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
        $app->urlManager->addRules([
                'agency' => 'agency/default/index',
                'podberem_kvartiry' => 'agency/default/select',
                'hochy_sdat_kvartiry' => 'agency/default/want-pass',
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
