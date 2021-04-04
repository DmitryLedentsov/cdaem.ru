<?php

namespace common\modules\site\models;

/**
 * Class Total
 * @package common\modules\site\models
 */
class Total extends \yii\base\Model
{
    /**
     * @return string
     */
    public static function getNamePartOfTheDay(): string
    {
        $h = date('H');

        if ($h >= 5 && $h <= 12) {
            return 'morning';
        }

        if ($h > 12 && $h <= 16) {
            return 'day';
        }

        if ($h > 16 && $h <= 21) {
            return 'evening';
        }

        return 'night';
    }
}
