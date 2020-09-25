<?php

namespace common\modules\users\models\frontend;

/**
 * Class ResendForm
 */
class ResendForm extends \nepster\users\models\frontend\ResendForm
{
    /**
     * @inheritdoc
     */
    public function resend()
    {
        if ($user = parent::resend()) {
            return $user;
        }

        return false;
    }
}
