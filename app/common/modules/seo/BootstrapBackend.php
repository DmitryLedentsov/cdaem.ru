<?php

namespace common\modules\seo;

use yii\helpers\Html;
use Yii;

/**
 * Bootstrap Backend
 * @package common\modules\geo
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $seoAccess = 1;

        if (!Yii::$app->user->can('seotext-view')
            && !Yii::$app->user->can('seo-specifications-view')
        ) {
            $seoAccess = 0;
        }

        // Add admin section
        $app->navigation->addSection([
            'module' => 'seo',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Сео',
            'icon' => '<i class="icon-text-width"></i>',
            'url' => \yii\helpers\Url::toRoute(['/seotext/default/index']),
            'options' => [],
            'access' => $seoAccess,
            'dropdown' => [
                [
                    'controller' => 'text',
                    'action' => 'index',
                    'name' => 'Сео-тексты',
                    'url' => \yii\helpers\Url::toRoute(['/seo/text/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('seotext-view'),
                ],
                [
                    'controller' => 'specification',
                    'action' => 'index',
                    'name' => 'Сео-спецификации',
                    'url' => \yii\helpers\Url::toRoute(['/seo/specification/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('seo-specifications-view'),
                ],
            ]
        ]);


        // Add module URL rules.
        $app->urlManager->addRules([

            ]
        );
    }
}
