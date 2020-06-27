<?php

namespace common\modules\seo;

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

            ]
        );
    }
}