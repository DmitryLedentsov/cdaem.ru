<?php

namespace common\modules\reviews;

use common\modules\reviews\models\Review;
use yii\helpers\Html;

/**
 * Bootstrap Backend
 * @package common\modules\reviews
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {

        $reviewsAccess = 1;

        if (!\Yii::$app->user->can('reviews-view')
            && !\Yii::$app->user->can('reviews-create')
        ) {
            $reviewsAccess = 0;
        }


        // Add admin section
        $app->navigation->addSection([
            'module' => 'reviews',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Отзывы',
            'icon' => '<i class="icon-pencil2"></i>',
            'url' => \yii\helpers\Url::toRoute(['/reviews/default/index']),
            'options' => [],
            'callback' => function ($params, $content) {
                return Html::tag('li', Html::a('<span class="label label-danger">' . Review::find()->moderation(0)->count() . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
            },
            'access' => $reviewsAccess,
            'dropdown' => [
                [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'Все отзывы',
                    'url' => \yii\helpers\Url::toRoute(['/reviews/default/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('reviews-view'),
                ],
                [
                    'controller' => 'default',
                    'action' => 'create',
                    'name' => 'Создать отзыв',
                    'url' => \yii\helpers\Url::toRoute(['/reviews/default/create']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('reviews-create'),
                ],
            ]
        ]);

        // Add module URL rules.
        $app->urlManager->addRules([

            ]
        );
    }
}
