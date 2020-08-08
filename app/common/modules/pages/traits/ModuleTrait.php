<?php

namespace common\modules\pages\traits;

use Yii;
use common\modules\pages\Module;

/**
 * Class ModuleTrait
 * @package common\modules\pages\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\pages\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\pages\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('pages');
            }
        }

        return $this->_module;
    }
}
