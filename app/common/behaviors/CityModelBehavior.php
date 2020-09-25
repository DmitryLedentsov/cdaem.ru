<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use common\modules\geo\models\City;

/**
 * City Model Behavior
 * @package common\behaviors
 */
class CityModelBehavior extends Behavior
{
    public $cityModel = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();


        $baseUrl = parse_url(Yii::$app->params['siteDomain']);
        $baseHost = $baseUrl['host'];

        $subDomain = str_replace('.' . $baseHost, '', $_SERVER['HTTP_HOST']);

        $this->cityModel = City::findByNameEng($subDomain);
    }

    /**
     * @param null $default
     * @return null
     */
    public function getCityId($default = null)
    {
        if (!$this->cityModel) {
            return $default;
        };

        return $this->cityModel->city_id;
    }
}
