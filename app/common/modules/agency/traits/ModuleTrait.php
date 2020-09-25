<?php

namespace common\modules\agency\traits;

use Yii;
use common\modules\pages\Module;

/**
 * Class ModuleTrait
 * @package common\modules\agency\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\agency\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\agency\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('agency');
            }
        }

        return $this->_module;
    }
}
