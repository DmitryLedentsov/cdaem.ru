<?php

namespace common\modules\office\traits;

use Yii;
use common\modules\office\Module;

/**
 * Class ModuleTrait
 * @package common\modules\office\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\office\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\office\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('office');
            }
        }

        return $this->_module;
    }
}
