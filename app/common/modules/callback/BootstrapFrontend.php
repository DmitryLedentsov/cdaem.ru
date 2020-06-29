<?php

namespace common\modules\callback;

/**
 * Bootstrap Frontend
 * @package common\modules\callback
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
                'callback' => '/callback/default/index'
            ]
        );
    }
}