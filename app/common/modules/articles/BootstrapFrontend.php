<?php

namespace common\modules\articles;

use Yii;

/**
 * Bootstrap Frontend
 * @package common\modules\articles
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
                'stati' => '/articles/default/index',
                'stati/<id>' => '/articles/default/view',
            ]
        );
    }
}
