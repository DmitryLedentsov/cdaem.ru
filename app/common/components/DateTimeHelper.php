<?php

namespace common\components;

use yii\i18n\Formatter;

/**
 * Class DateTimeHelper
 * @package common\components
 */
class DateTimeHelper extends \nepster\basis\helpers\DateTimeHelper
{
    /**
     * Возвращает строку даты
     *
     * Например: 9 мая
     *
     * @param $datetime
     * @return string
     */
    public static function toDayAndMonth($datetime)
    {
        $formatter = self::getExtendFormatter($datetime);

        return $formatter->asDate($datetime, 'php:j F');
    }

    /**
     * Возвращает строку даты
     *
     * Например: 9 мая
     *
     * @param $datetime
     * @return string
     */
    public static function toWeek($datetime)
    {
        $formatter = self::getExtendFormatter($datetime);

        return $formatter->asDate($datetime, 'php:l');
    }

    /**
     * Инициализирует Yii2 Formatter
     *
     * @param $datetime
     * @return Formatter
     */
    private static function getExtendFormatter($datetime)
    {
        $formatter = new Formatter();

        if (!is_numeric($datetime)) {
            $formatter->timeZone = 'UTC';
        }

        return $formatter;
    }
}
