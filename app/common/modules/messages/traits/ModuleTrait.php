<?php

namespace common\modules\messages\traits;

use common\modules\messages\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package common\modules\messages\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\messages\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\messages\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('messages');
            }
        }
        return $this->_module;
    }
}
