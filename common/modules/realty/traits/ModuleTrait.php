<?php

namespace common\modules\realty\traits;

use common\modules\realty\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package common\modules\realty\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\realty\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\realty\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('realty');
            }
        }
        return $this->_module;
    }
}
