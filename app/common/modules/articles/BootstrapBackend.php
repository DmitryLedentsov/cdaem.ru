<?php

namespace common\modules\articles;

use yii\helpers\Html;

/**
 * Bootstrap Backend
 * @package common\modules\articles
 */
class BootstrapBackend implements \yii\base\BootstrapInterface {

    /**
     * @inheritdoc
     */
    public function bootstrap($app) {
        $articlesAccess = 1;

        if (!\Yii::$app->user->can('articles-view') && !\Yii::$app->user->can('articles-create')
        ) {
            $articlesAccess = 0;
        }


        // Add admin section
        $app->navigation->addSection([
            'module' => 'articles',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Статьи',
            'icon' => '<i class="icon-pencil3"></i>',
            'url' => \yii\helpers\Url::toRoute(['/articles/default/index']),
            'options' => [],
            'access' => $articlesAccess,
            'dropdown' => [
                    [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'Все статьи',
                    'url' => \yii\helpers\Url::toRoute(['/articles/default/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('articles-view'),
                ],
                    [
                    'controller' => 'default',
                    'action' => 'create',
                    'name' => 'Создать статью',
                    'url' => \yii\helpers\Url::toRoute(['/articles/default/create']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('articles-create'),
                ],
                
                   [
                    'controller' => 'articlelink',
                    'action' => 'create',
                    'name' => 'Весь рекламный контент',
                    'url' => \yii\helpers\Url::toRoute(['/articles/articlelink/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('articles-create'),
                ],
                    [
                    'controller' => 'articlelink',
                    'action' => 'create',
                    'name' => 'Создать рекламный контент',
                    'url' => \yii\helpers\Url::toRoute(['/articles/articlelink/create']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('articles-create'),
                ],
            ]
        ]);

        // Add module URL rules.
        $app->urlManager->addRules([
                ]
        );
    }

}
