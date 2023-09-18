<?php

namespace common\modules\partners;

use Yii;

/**
 * Partners module bootstrap class.
 * @package common\modules\partners
 */
class BootstrapFrontend implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->urlManager->addRules(
            [
                '/search' => '/partners/default/index',

                'office/apartments/<filter>' => 'partners/office/apartments',
                'office/apartments' => 'partners/office/apartments',
                'office/apartment/create' => '/partners/office/create',
                'office/apartment/<id:\d+>' => 'partners/office/preview',
                'office/apartment/<id:\d+>/update' => '/partners/office/update',
                'office/calendar' => '/partners/office/calendar',

                'office/reservations' => 'partners/reservation/reservations',
                'office/control-image/<action:\w+>/<id:\d+>' => '/partners/ajax/control-image',

                Yii::$app->params['siteSubDomain'] . '/office/total-bid/<find:\w+>' => 'partners/reservation/total-bid',
                'office/total-bid/<find:\w+>' => 'partners/reservation/total-bid',
                Yii::$app->params['siteSubDomain'] . '/office/total-bid' => 'partners/reservation/total-bid',
                'office/total-bid' => 'partners/reservation/total-bid',

                Yii::$app->params['siteSubDomain'] . '/reservation/<advert_id:\d+>' => '/partners/reservation/advert-reservation',
                'reservation/<advert_id:\d+>' => '/partners/reservation/advert-reservation',

                Yii::$app->params['siteSubDomain'] . '/reservation' => '/partners/reservation/index',
                'reservation' => '/partners/reservation/index',


                //'partner_thumb/<path:.*>' => '/partners/default/thumbs',
                'partners/pre-region' => '/partners/default/pre-region',

                'partners/ajax/reservation-failure/<id:\d+>' => '/partners/ajax/reservation-failure',
                'partners/ajax/<a:\w+>' => '/partners/ajax/<a>',

                Yii::$app->params['siteSubDomain'] => '/partners/default/region',
            ]
        );
    }
}
