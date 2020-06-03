<?php

namespace frontend\modules\site;

use Yii;

/**
 * Bootstrap
 * @package frontend\modules\site
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->urlManager->addRules([
                'partnership' => '/site/default/partnership',
                'badbrowser' => '/site/default/badbrowser',
                'msk-taxi' => '/site/default/taxi',
                'sitemap<city>.xml' => '/site/default/sitemap',
                'sitemap.xml' => '/site/default/sitemap',
            ]
        );
    }
}
