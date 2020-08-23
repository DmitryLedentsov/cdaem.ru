<?php

namespace common\modules\office\widgets\frontend;

use Yii;
use yii\base\Widget;
use common\modules\users\models\Profile;
use common\modules\messages\models\Mailbox;
use frontend\modules\partners\models\Reservation;
use frontend\modules\partners\models\AdvertReservation;

/**
 * Class GraphicalMenu
 * @package common\modules\office\widgets\frontend
 */
class GraphicalMenu extends Widget
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

        return $this->render('graphicalMenu', [
            'reservationsForAll' => $reservationsForAll,
            'reservationsForAdvert' => $reservationsForAdvert,
            'newMessagesCount' => $newMessagesCount,
        ]);
    }
}
