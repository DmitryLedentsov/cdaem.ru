<?php

namespace common\behaviors;

use Yii;

/**
 * Class BasisBehavior
 * @package common\behaviors
 */
class BasisBehavior extends \yii\base\Behavior
{
    public function asBasisFullDateTime($date)
    {
        return Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($date);
    }

    public function asBasisDiffAgoPeriodRound($date)
    {
        return Yii::$app->BasisFormat->helper('DateTime')->diffAgoPeriodRound($date);
    }

    public function asBasisBooleanString($string)
    {
        return Yii::$app->BasisFormat->helper('Status')->booleanString($string);
    }
}
