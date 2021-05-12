<?php

namespace common\modules\users\models\frontend;

use RuntimeException;
use nepster\users\models\User;

/**
 * Class RecoveryForm
 * @package common\modules\users\models\frontend
 */
class RecoveryForm extends \nepster\users\models\frontend\RecoveryForm
{
    /**
     * @inheritdoc
     */
    public function recovery(): User
    {
        if ($user = parent::recovery()) {
            return $user;
        }

        throw new RuntimeException('RecoveryForm error.');
    }
}
