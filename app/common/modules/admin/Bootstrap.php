<?php

namespace common\modules\admin;

/**
 * Admin module bootstrap class.
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->urlManager->addRules(
            [
                '' => 'admin/default/index',
            ]
        );

        $accessLog = \Yii::$app->user->can('logs-view');


        // Add admin section
        $app->navigation->addSection([
            'module' => 'admin',
            'controller' => 'default',
            'action' => 'log',
            'name' => 'Управление',
            'icon' => '<i class="icon-settings"></i>',
            'url' => \yii\helpers\Url::toRoute(['/admin/default/index']),
            'options' => [],
            'access' => 1,
            'dropdown' => [

                [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'На главную',
                    'url' => \yii\helpers\Url::toRoute(['/admin/default/index']),
                    'options' => [],
                    'access' => 1,
                ],

                [
                    'controller' => 'default',
                    'action' => 'log',
                    'name' => 'Просмотр логов',
                    'url' => \yii\helpers\Url::toRoute(['/admin/default/log']),
                    'options' => [],
                    'access' => $accessLog,
                ],
            ],
        ]);
    }
}
