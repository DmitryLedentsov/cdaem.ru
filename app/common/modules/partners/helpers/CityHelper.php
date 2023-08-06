<?php

namespace common\modules\partners\helpers;
use wapmorgan\yii2inflection\Inflector;
use Yii;
class CityHelper
{
    public static function formatInPrepositionalCase(string $name) :string{
        return Yii::$app->inflection->inflectGeoName($name, Inflector::PREPOSITIONAL);
    }
}