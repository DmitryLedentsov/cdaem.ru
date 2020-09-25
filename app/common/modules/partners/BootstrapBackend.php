<?php

namespace common\modules\partners;

use yii\helpers\Html;

/**
 * Partners module bootstrap class.
 * @package common\modules\partners
 */
class BootstrapBackend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $partnersAccess = 1;

        if (
            !\Yii::$app->user->can('partners-apartment-view')
            && !\Yii::$app->user->can('partners-reservation-view')
        ) {
            $partnersAccess = 0;
        }


        // Add admin section
        $app->navigation->addSection([
            'module' => 'partners',
            'controller' => 'default',
            'action' => 'index',
            'name' => 'Доска обьявлений',
            'icon' => '<i class="icon-home2"></i>',
            'url' => \yii\helpers\Url::toRoute(['/partners/default/index']),
            'options' => [],
            'access' => $partnersAccess,
            'callback' => function ($params, $content) {
                return Html::tag('li', Html::a('<span class="label label-danger">' . models\Apartment::find()->status(0)->joinWith([
                            'user' => function ($query) {
                                $query->joinWith([
                                    'profile' => function ($query) {
                                        $query->partner(\common\modules\users\models\Profile::NO_PARTNER);
                                    },
                                ]);
                            },
                        ])->count() . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
            },
            'dropdown' => [
                [
                    'controller' => 'default',
                    'action' => 'index',
                    'name' => 'Все апартаменты',
                    'url' => \yii\helpers\Url::toRoute(['/partners/default/index']),
                    'options' => [],
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a('<span class="label label-success">' . models\Apartment::find()->status(1)->joinWith([
                                    'user' => function ($query) {
                                        $query->joinWith([
                                            'profile' => function ($query) {
                                                $query->partner(\common\modules\users\models\Profile::NO_PARTNER);
                                            },
                                        ]);
                                    },
                                ])->count() . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
                    },
                    'access' => \Yii::$app->user->can('partners-apartment-view'),
                ],
                [
                    'controller' => 'reservation',
                    'action' => 'index',
                    'name' => 'Заявки на бронь',
                    'url' => \yii\helpers\Url::toRoute(['/partners/reservation/index']),
                    'options' => [],
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a('<span class="label label-success">' . models\Reservation::find()->notClosedAndCancel()->count() . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
                    },
                    'access' => \Yii::$app->user->can('partners-reservation-view'),
                ],
                [
                    'controller' => 'advert-reservation',
                    'action' => 'index',
                    'name' => 'Заявки на бронь к объявлениям',
                    'url' => \yii\helpers\Url::toRoute(['/partners/advert-reservation/index']),
                    'options' => [],
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a('<span class="label label-success">' . models\AdvertReservation::find()->closed(0)->cancel(0)->count() . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
                    },
                    'access' => \Yii::$app->user->can('partners-reservation-view'),
                ],
                [
                    'controller' => 'reservation-failures',
                    'action' => 'index',
                    'name' => 'Заявки "Незаезд"',
                    'url' => \yii\helpers\Url::toRoute(['/partners/reservation-failures/index']),
                    'options' => [],
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a('<span class="label label-success">' . models\ReservationFailure::find()->processed(0)->closed(1)->count() . '</span> ' . $params['name'] . $params['icon'], $params['url']) . $content, $params['options']);
                    },
                    'access' => \Yii::$app->user->can('partners-reservation-view'),
                ],
                /*[
                    'controller' => 'default',
                    'action' => 'partners',
                    'name' => 'Партнеры',
                    'url' => \yii\helpers\Url::toRoute(['/agency/default/partners']),
                    'options' => [],
                    'callback' => function ($params, $content) {
                        return Html::tag('li', Html::a($params['name'] . $params['icon'] . '<span class="label label-success">'.models\Apartment::find()->joinWith([
                            'user' => function ($query) {
                                $query->joinWith([
                                    'profile' => function ($query) {
                                        $query->partner();
                                    },
                                ]);
                            },
                        ])->count().'</span>', $params['url']) . $content, $params['options']);
                    },
                    'access' => 1,
                ],*/
            ]
        ]);

        // Add module URL rules.
        $app->urlManager->addRules(
            [

            ]
        );
    }
}
