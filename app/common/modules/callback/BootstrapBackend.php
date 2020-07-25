<?php

namespace common\modules\callback;

use common\modules\callback\models\Callback;
use yii\helpers\Html;
use Yii;

/**
 * Bootstrap Backend
 * @package common\modules\callback
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $countCallback = Callback::find()
            ->where(['active' => Callback::UNPROCESSED])
            ->count();

        // Add admin section
        $app->navigation->addSection([
            'module' => 'callback',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Обратный звонок',
            'icon' => '<i class="icon-phone"></i>',
            'url' => \yii\helpers\Url::toRoute(['/callback/default/index']),
            'options' => [],
            'callback' => function ($params, $content) use ($countCallback) {
                return Html::tag('li', Html::a('<span class="label label-danger">' . $countCallback . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
            },
            'dropdown' => [],
            'access' => \Yii::$app->user->can('callback-view'),
        ]);

        // Add module URL rules.
        $app->urlManager->addRules([

            ]
        );

    }
}
