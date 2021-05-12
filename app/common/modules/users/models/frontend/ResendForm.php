<?php

namespace common\modules\users\models\frontend;

use RuntimeException;
use nepster\users\models\User;

/**
 * Class ResendForm
 * @package common\modules\users\models\frontend
 */
class ResendForm extends \nepster\users\models\frontend\ResendForm
{
    /**
     * @inheritdoc
     */
    public function resend(): User
    {
        if ($user = parent::resend()) {
            return $user;
        }

        throw new RuntimeException('ResendForm error.');
    }
}
