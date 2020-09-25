<?php

namespace common\modules\reviews;

/**
 * Bootstrap Frontend
 * @package common\modules\reviews
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
                'office/reviews/<id:\d+>' => 'reviews/default/index',
                'office/reviews' => 'reviews/default/index',
                'create-review/<apartment_id:\d+>' => '/reviews/ajax/create',
            ]
        );
    }
}
