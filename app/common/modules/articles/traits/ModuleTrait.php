<?php

namespace common\modules\articles\traits;

use common\modules\articles\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package common\modules\articles\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\articles\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\articles\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('articles');
            }
        }
        return $this->_module;
    }
}
