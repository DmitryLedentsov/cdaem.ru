<?php

namespace backend\modules\merchant;

/**
 * Class Bootstrap
 * @package backend\modules\merchant
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add admin section
        $app->navigation->addSection([
            'module' => 'merchant',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Мерчант',
            'icon' => '<i class="icon-credit"></i>',
            'url' => \yii\helpers\Url::toRoute(['/merchant/default/index']),
            'options' => [],
            'access' => \Yii::$app->user->can('merchant-payment-view'),
            'dropdown' => [
                [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'История денежного оборота',
                    'url' => \yii\helpers\Url::toRoute(['/merchant/default/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('merchant-payment-view'),
                ],
                [
                    'controller' => 'default',
                    'action' => 'service',
                    'name' => 'История оплаты сервисов',
                    'url' => \yii\helpers\Url::toRoute(['/merchant/default/service']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('merchant-service-view'),
                ],
            ]
        ]);

        // Add module URL rules.
        $app->urlManager->addRules(
            [

            ]
        );
    }
}
