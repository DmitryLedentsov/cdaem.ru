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
                'office/create-adverts' => '/partners/default/create',
                'office/update-adverts/<id:\d+>' => '/partners/default/update',
                'office/apartments/<filter>' => 'partners/default/apartments',
                'office/apartments' => 'partners/default/apartments',
                'office/control-image/<action:\w+>/<id:\d+>' => '/partners/ajax/control-image',
                'office/calendar' => '/partners/default/calendar',

                'office/reservations' => 'partners/reservation/reservations',

                Yii::$app->params['siteSubDomain'] . '/office/total-bid/<find:\w+>' => 'partners/reservation/total-bid',
                'office/total-bid/<find:\w+>' => 'partners/reservation/total-bid',
                Yii::$app->params['siteSubDomain'] . '/office/total-bid' => 'partners/reservation/total-bid',
                'office/total-bid' => 'partners/reservation/total-bid',

                Yii::$app->params['siteSubDomain'] . '/reservation/<advert_id:\d+>' => '/partners/reservation/advert-reservation',
                'reservation/<advert_id:\d+>' => '/partners/reservation/advert-reservation',

                Yii::$app->params['siteSubDomain'] . '/reservation' => '/partners/reservation/index',
                'reservation' => '/partners/reservation/index',

                '/search' => '/partners/default/index',
                Yii::$app->params['siteSubDomain'] . '/flats/<id:\d+>' => '/partners/default/view',
                Yii::$app->params['siteSubDomain'] . '/flats/<id:\d+>/others' => '/partners/default/others',

                //'partner_thumb/<path:.*>' => '/partners/default/thumbs',
                'partners/pre-region' => '/partners/default/pre-region',

                'partners/ajax/reservation-failure/<id:\d+>' => '/partners/ajax/reservation-failure',
                'partners/ajax/<a:\w+>' => '/partners/ajax/<a>',

                Yii::$app->params['siteSubDomain'] => '/partners/default/region',
            ]
        );
    }
}
