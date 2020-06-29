<?php

namespace common\modules\helpdesk;

use common\modules\helpdesk\models\Helpdesk;
use yii\helpers\Html;

/**
 * Bootstrap Backend
 * @package common\modules\helpdesk
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $count = Helpdesk::find()
            ->where(['answered' => Helpdesk::AWAITING])
            ->count();

        // Add admin section
        $app->navigation->addSection([
            'module' => 'helpdesk',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Техподдержка',
            'icon' => '<i class="icon-support"></i>',
            'url' => \yii\helpers\Url::toRoute(['/helpdesk/default/index']),
            'options' => [],
            'callback' => function ($params, $content) use ($count) {
                return Html::tag('li', Html::a('<span class="label label-danger">' . $count . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
            },
            'dropdown' => [],
            'access' => \Yii::$app->user->can('helpdesk-view'),
        ]);

        // Add module URL rules.
        $app->urlManager->addRules([

            ]
        );
    }
}
