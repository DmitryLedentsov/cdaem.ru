<?php

namespace common\modules\callback\traits;

use common\modules\callback\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package common\modules\callback\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\callback\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\callback\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('callback');
            }
        }
        return $this->_module;
    }
}
