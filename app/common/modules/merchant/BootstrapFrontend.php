<?php

namespace common\modules\merchant;

/**
 * Class BootstrapFrontend
 * @package frontend\modules\merchant
 */
class BootstrapFrontend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Установка текущей валюты
        //Yii::$app->formatter->currencyCode = Yii::$app->getModule('merchant')->viewMainCurrency;

        // Add module URL rules.
        $app->urlManager->addRules(
            [
                'merchant/payment/service' => 'merchant/default/service',
                'merchant/payments/<page:\d+>' => 'merchant/default/index',
                'merchant/payments' => 'merchant/default/index',
                'merchant/pay' => 'merchant/default/pay',
                'merchant/payment-widget' => 'merchant/default/payment-widget',
            ]
        );
    }
}
