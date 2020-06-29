<?php

namespace common\modules\users;

/**
 * Users module bootstrap class.
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if (isset($app->navigation)) {
            // Add admin section
            $app->navigation->addSection([
                'module' => 'users',
                'controller' => 'default',
                'action' => 'index',
                'name' => 'Пользователи',
                'icon' => '<i class="icon-users"></i>',
                'url' => \yii\helpers\Url::toRoute(['/users/user/index']),
                'options' => [],
                'access' => function () {
                    return true;
                },
                'dropdown' => [
                    [
                        'controller' => 'user',
                        'action' => 'index',
                        'name' => 'Все пользователи',
                        'url' => \yii\helpers\Url::toRoute(['/users/user/index']),
                        'options' => [],
                    ],
                    [
                        'controller' => 'action',
                        'action' => 'index',
                        'name' => 'Действия',
                        'url' => \yii\helpers\Url::toRoute(['/users/action/index']),
                        'options' => [],
                    ],
                    [
                        'controller' => 'rbac',
                        'action' => 'index',
                        'name' => 'Права доступа',
                        'url' => \yii\helpers\Url::toRoute(['/users/rbac/index']),
                        'options' => [],
                    ],
                ]
            ]);
        }


        // Register translations
        $app->i18n->translations['users*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@nepster/users/messages',
            'fileMap' => [
                'users.rbac' => 'rbac.php',
            ],
        ];

        // Add module URL rules.
        $app->urlManager->addRules([
                'activation/<token>' => 'users/guest/activation',
                'recovery-confirmation/<token>' => 'users/guest/recovery-confirmation',
                '<_a:(login|signup|activation|resend|recovery)>' => 'users/guest/<_a>',
                '<_a:logout|profile|password|legal-person>' => 'users/user/<_a>',
            ]
        );
    }
}
