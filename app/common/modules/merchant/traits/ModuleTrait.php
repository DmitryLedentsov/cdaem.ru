<?php

namespace common\modules\merchant\traits;

use Yii;
use common\modules\merchant\Module;

/**
 * Class ModuleTrait
 * @package common\modules\merchant\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\merchant\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\merchant\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('merchant');
            }
        }

        return $this->_module;
    }
}
