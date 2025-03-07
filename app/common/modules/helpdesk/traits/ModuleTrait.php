<?php

namespace common\modules\helpdesk\traits;

use Yii;
use common\modules\helpdesk\Module;

/**
 * Class ModuleTrait
 * @package common\modules\helpdesk\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\helpdesk\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\helpdesk\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('helpdesk');
            }
        }

        return $this->_module;
    }
}
