<?php

namespace common\modules\reviews\traits;

use common\modules\pages\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package common\modules\reviews\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\reviews\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\reviews\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('reviews');
            }
        }
        return $this->_module;
    }
}
