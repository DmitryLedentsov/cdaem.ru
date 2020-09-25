<?php

namespace common\modules\seo;

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
        $app->urlManager->addRules(
            [

            ]
        );
    }
}
