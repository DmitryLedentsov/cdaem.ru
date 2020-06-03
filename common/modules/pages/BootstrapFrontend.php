<?php

namespace common\modules\pages;

use Yii;

/**
 * Bootstrap Frontend
 * @package common\modules\pages
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
                // Импорт старых правил
                ['pattern' => 'about',       'route' => 'pages/default/index', 'defaults' => ['url' => 'about']],
                ['pattern' => 'contacts',    'route' => 'pages/default/index', 'defaults' => ['url' => 'contacts']],
                ['pattern' => 'privacy',     'route' => 'pages/default/index', 'defaults' => ['url' => 'privacy']],
                ['pattern' => 'booking',     'route' => 'pages/default/index', 'defaults' => ['url' => 'booking']],
                ['pattern' => 'rules',       'route' => 'pages/default/index', 'defaults' => ['url' => 'rules']],
                ['pattern' => 'exhibitions', 'route' => 'pages/default/index', 'defaults' => ['url' => 'exhibitions']],
                ['pattern' => 'airports',    'route' => 'pages/default/index', 'defaults' => ['url' => 'airports']],
                ['pattern' => 'railway',     'route' => 'pages/default/index', 'defaults' => ['url' => 'railway']],
                ['pattern' => 'partnership',     'route' => 'pages/default/index', 'defaults' => ['url' => 'partnership']],

                // Основные правила
                'page/<url>' => 'pages/default/index',
            ]
        );

        // Register translations
        $app->i18n->translations['pages*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@common/modules/pages/messages',
            'fileMap' => [
                // path you files
            ],
        ];
    }
}