<?php

namespace common\modules\pages;

use common\modules\helpdesk\models\Helpdesk;
use yii\helpers\Html;

/**
 * Bootstrap Backend
 * @package common\modules\pages
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $pagesAccess = 1;

        if (!\Yii::$app->user->can('pages-view')
            && !\Yii::$app->user->can('pages-create')
        ) {
            $pagesAccess = 0;
        }

        // Add admin section
        $app->navigation->addSection([
            'module' => 'pages',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Статические страницы',
            'icon' => '<i class="icon-file4"></i>',
            'url' => \yii\helpers\Url::toRoute(['/pages/default/index']),
            'options' => [],
            'access' => $pagesAccess,
            'dropdown' => [
                [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'Все страницы',
                    'url' => \yii\helpers\Url::toRoute(['/pages/default/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('pages-view'),
                ],
                [
                    'controller' => 'default',
                    'action' => 'create',
                    'name' => 'Создать страницу',
                    'url' => \yii\helpers\Url::toRoute(['/pages/default/create']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('pages-create'),
                ],
            ]
        ]);

        // Register translations
        $app->i18n->translations['pages*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@common/modules/pages/messages',
            'fileMap' => [
                // path you files
            ],
        ];

        // Add module URL rules.
        $app->urlManager->addRules([

            ]
        );
    }
}
