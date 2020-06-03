<?php

namespace common\modules\partners\traits;

use common\modules\partners\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package common\modules\partners\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\partners\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\partners\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('partners');
            }
        }
        return $this->_module;
    }
}
