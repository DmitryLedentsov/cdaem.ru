<?php

namespace common\modules\realty;

use yii\helpers\Html;

/**
 * Realty module bootstrap class.
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app->navigation) {

            $realtyAccess = 1;

            if (!\Yii::$app->user->can('realty-rent-type-view')
            ) {
                $realtyAccess = 0;
            }

            // Add admin section
            $app->navigation->addSection([
                'module' => 'realty',
                'controller' => 'default',
                'action' => 'index',
                'name' => 'Недвижимость',
                'icon' => '<i class="icon-home6"></i>',
                'url' => \yii\helpers\Url::toRoute(['/realty/default/index']),
                'options' => [],
                'access' => $realtyAccess,
                'dropdown' => [
                    [
                        'controller' => 'default',
                        'action' => 'index',
                        'name' => 'Типы аренды',
                        'url' => \yii\helpers\Url::toRoute(['/realty/default/index']),
                        'options' => [],
                        'access' => \Yii::$app->user->can('realty-rent-type-view'),
                    ],
                ]
            ]);
        }


        // Add module URL rules.
        $app->urlManager->addRules([

            ]
        );
    }
}
