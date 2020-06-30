<?php

namespace frontend\modules\site\models;

use Yii;

/**
 * Общая модель
 */
class Total extends \yii\base\Model
{
    /**
     * Получить именование части суток
     * @return string
     */
    public static function getNamePartOfTheDay()
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
