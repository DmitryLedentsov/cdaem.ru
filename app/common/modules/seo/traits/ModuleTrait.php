<?php

namespace common\modules\seo\traits;

use Yii;
use common\modules\seo\Module;

/**
 * Class ModuleTrait
 * @package common\modules\seo\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \common\modules\seo\Module|null Module instance
     */
    private $_module;

    /**
     * @return \common\modules\seo\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('seo');
            }
        }

        return $this->_module;
    }
}
