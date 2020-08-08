<?php

namespace frontend\modules\office\widgets;

use Yii;
use yii\base\Widget;
use common\modules\users\models\Profile;
use common\modules\messages\models\Mailbox;
use frontend\modules\partners\models\Reservation;
use frontend\modules\partners\models\AdvertReservation;

/**
 * Class GraphicalMenu2
 * @package frontend\modules\office\widgets
 */
class GraphicalMenu2 extends Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->user->identity->profile->user_type === Profile::WANT_RENT) {
            $reservationsForAll = Reservation::nonViewedCount('renter');
            $reservationsForAdvert = AdvertReservation::nonViewedCount('renter');
        } else {
            $reservationsForAll = Reservation::nonViewedCount('owner', Yii::$app->request->cityId);
            $reservationsForAdvert = AdvertReservation::nonViewedCount('landlord');
        }

        $newMessagesCount = MailBox::find()->thisUser()->unread()->count();

        return $this->render('graphicalMenu_2', [
            'reservationsForAll' => $reservationsForAll,
            'reservationsForAdvert' => $reservationsForAdvert,
            'newMessagesCount' => $newMessagesCount,
        ]);
    }
}
