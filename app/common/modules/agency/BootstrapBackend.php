<?php

namespace common\modules\agency;

use yii\helpers\Html;
use common\modules\agency\models as models;

/**
 * Bootstrap Backned
 * @package common\modules\agency
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $agencyAccess = 1;

        if (!\Yii::$app->user->can('agency-details-history-view')
            && !\Yii::$app->user->can('agency-apartment-view')
            && !\Yii::$app->user->can('agency-reservation-view')
            && !\Yii::$app->user->can('agency-special-advert-view')
            && !\Yii::$app->user->can('agency-advertisement-view')
            && !\Yii::$app->user->can('agency-select-view')
            && !\Yii::$app->user->can('agency-want-pass-view')
            && !\Yii::$app->user->can('agency-details-history-view')
        ) {
            $agencyAccess = 0;
        }


        // Add admin section
        $app->navigation->addSection([
            'module' => 'agency',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Агентство',
            'icon' => '<i class="icon-home6"></i>',
            'url' => \yii\helpers\Url::toRoute(['/agency/default/index']),
            'options' => [],
            'access' => $agencyAccess,
            'dropdown' => [

                [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'Все апартаменты',
                    'url' => \yii\helpers\Url::toRoute(['/agency/default/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-apartment-view'),
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a($params['name'] . $params['icon'] . '<span class="label label-success">' . models\Apartment::find()->count() . '</span>', $params['url']) . $content, $params['options']);
                    }
                ],

                [
                    'controller' => 'reservation',
                    'action' => 'index',
                    'name' => 'Бронь',
                    'url' => \yii\helpers\Url::toRoute(['/agency/reservation/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-reservation-view'),
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a($params['name'] . $params['icon'] . '<span class="label label-success">' . models\Reservation::find()->processed(0)->count() . '</span>', $params['url']) . $content, $params['options']);
                    }
                ],

                [
                    'controller' => 'specials',
                    'action' => 'index',
                    'name' => 'Спецпредложения',
                    'url' => \yii\helpers\Url::toRoute(['/agency/specials/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-special-advert-view'),
                ],

                [
                    'controller' => 'advertisement',
                    'action' => 'index',
                    'name' => 'Реклама',
                    'url' => \yii\helpers\Url::toRoute(['/agency/advertisement/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-advertisement-view'),
                ],

                [
                    'controller' => 'select',
                    'action' => 'index',
                    'name' => 'Подберем квартиру',
                    'url' => \yii\helpers\Url::toRoute(['/agency/select/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-select-view'),
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a($params['name'] . $params['icon'] . '<span class="label label-success">' . models\Select::find()->where(['status' => 0])->count() . '</span>', $params['url']) . $content, $params['options']);
                    }
                ],

                [
                    'controller' => 'want-pass',
                    'action' => 'index',
                    'name' => 'Хочу сдать квартиру',
                    'url' => \yii\helpers\Url::toRoute(['/agency/want-pass/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-want-pass-view'),
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a($params['name'] . $params['icon'] . '<span class="label label-success">' . models\WantPass::find()->where(['status' => 0])->count() . '</span>', $params['url']) . $content, $params['options']);
                    }
                ],

                [
                    'controller' => 'details',
                    'action' => 'index',
                    'name' => 'Заявки на реквизиты',
                    'url' => \yii\helpers\Url::toRoute(['/agency/details/index']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-details-history-view'),
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a($params['name'] . $params['icon'] . '<span class="label label-success">' . models\DetailsHistory::find()->where(['processed' => 0])->count() . '</span>', $params['url']) . $content, $params['options']);
                    }
                ],

                [
                    'controller' => 'details',
                    'action' => 'update-files',
                    'name' => 'Редактирование реквизитов',
                    'url' => \yii\helpers\Url::toRoute(['/agency/details/update-files']),
                    'options' => [],
                    'access' => \Yii::$app->user->can('agency-details-history-view'),
                ],
            ]
        ]);

        // Add module URL rules.
        $app->urlManager->addRules(
            [

            ]
        );
    }
}
