<?php

namespace common\modules\partners\helpers;

use Yii;
use common\modules\geo\models\City;
use wapmorgan\yii2inflection\Inflector;

class CityHelper
{
    public static function formatInPrepositionalCase(string $name) :string
    {
        return Yii::$app->inflection->inflectGeoName($name, Inflector::PREPOSITIONAL);
    }
}
